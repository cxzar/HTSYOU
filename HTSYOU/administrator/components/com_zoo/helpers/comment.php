<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

/*
	Class: CommentHelper
		Helper class for comments
*/
class CommentHelper extends AppHelper {

	/*
       Class constants
    */
	const COOKIE_PREFIX   = 'zoo-comment_';
	const COOKIE_LIFETIME = 15552000; // 6 months

    /*
		Variable: _author
			Active author.
    */
	protected $_author;

	/*
		Function: renderComments
			Render comments and respond form html.

		Returns:
			String
	*/
	public function renderComments($view, $item) {

		if ($item->getApplication()->isCommentsEnabled()) {

			// get application params
			$params = $this->app->parameter->create($item->getApplication()->getParams()->get('global.comments.'));

			if ($params->get('twitter_enable') && !function_exists('curl_init')) {
				$this->app->error->raiseWarning(500, JText::_('To use Twitter, CURL needs to be enabled in your php settings.'));
				$params->set('twitter_enable', false);
			}

			// get active author
			$active_author = $this->activeAuthor();

			// get comment content from session
			$content = $this->app->system->session->get('com_zoo.comment.content');
			$params->set('content', $content);

			// get comments and build tree
			$comments = $item->getCommentTree(Comment::STATE_APPROVED);

			if ($item->isCommentsEnabled() || count($comments)-1) {
				// create comments html
				return $view->partial('comments', compact('item', 'active_author', 'comments', 'params'));
			}
		}

		return null;

	}

	/*
		Function: activeAuthor
			Retrieve currently active author object.

		Returns:
			CommentAuthor
	*/
	public function activeAuthor() {

		if (!isset($this->_author)) {

			// get login (joomla users always win)
			$login = $this->app->request->getString(self::COOKIE_PREFIX.'login', '', 'cookie');

			// get active user
			$user = $this->app->user->get();

			if ($user->id) {

				// create author object from user
				$this->_author = $this->app->commentauthor->create('joomla', array($user->name, $user->email, '', $user->id));

			} else if ($login == 'facebook'
						&& ($connection = $this->app->facebook->client())
						&& ($content = $connection->getCurrentUserProfile())
						&& isset($content->id)
						&& isset($content->name)) {

				// create author object from facebook user id
				$this->_author = $this->app->commentauthor->create('facebook', array($content->name, null, null, $content->id));

			} else if ($login == 'twitter'
						&& ($connection = $this->app->twitter->client())
						&& ($content = $connection->get('account/verify_credentials'))
						&& isset($content->screen_name)
						&& isset($content->id)) {

				// create author object from twitter user id
				$this->_author = $this->app->commentauthor->create('twitter', array($content->screen_name, null, null, $content->id));

			} else {

				$this->app->twitter->logout();
				$this->app->facebook->logout();

				// create author object from cookies
				$cookie = $this->readCookies();
				$this->_author = $this->app->commentauthor->create('', array($cookie['author'], $cookie['email'], $cookie['url']));

			}
		}

		setcookie(self::COOKIE_PREFIX.'login', $this->_author->getUserType(), time() + self::COOKIE_LIFETIME, '/');

		return $this->_author;
	}

	/*
		Function: readCookies
			Retrieve author, email, url from cookie.

		Returns:
			Array
	*/
	public function readCookies() {

		// get cookies
		foreach (array('hash', 'author', 'email', 'url') as $key) {
			$data[$key] = $this->app->request->getString(self::COOKIE_PREFIX.$key, '', 'cookie');
		}

		// verify hash
		if ($this->getCookieHash($data['author'], $data['email'], $data['url']) == $data['hash']) {
			return $data;
		}

		return array('hash' => null, 'author' => null, 'email' => null, 'url' => null);
	}

	/*
		Function: saveCookies
			Save author, email, url as cookie.

		Parameters:
			$data - Cookie data

		Returns:
			Void
	*/
	public function saveCookies($author, $email, $url) {

		$hash = $this->getCookieHash($author, $email, $url);

		// set cookies
		foreach (compact('hash', 'author', 'email', 'url') as $key => $value) {
			setcookie(self::COOKIE_PREFIX.$key, $value, time() + self::COOKIE_LIFETIME);
		}

	}

	/*
		Function: getCookieHash
			Retrieve hash of author and email.

		Parameters:
			$author - Author
			$email - Email
			$url - URL

		Returns:
			String
	*/
	public function getCookieHash($author, $email, $url) {

		// get secret from config
		$secret = $this->app->system->config->getValue('config.secret');

		return md5($author.$email.$url.$secret);
	}

	/*
		Function: matchWords
			Match words against comments content, author, URL, Email or IP.

		Parameters:
			$comments - Comment
			$words - Words to match against

		Returns:
			Boolean
	*/
	public function matchWords($comment, $words) {

		$vars = array('author', 'email', 'url', 'ip', 'content');

		if ($words = explode("\n", $words)) {
			foreach ($words as $word) {
				if ($word = trim($word)) {

					$pattern = '/'.preg_quote($word).'/i';

					foreach ($vars as $var) {
						if (preg_match($pattern, $comment->$var)) {
							return true;
						}
					}
				}
			}
		}

		return false;
	}

	/*
		Function: filterContentInput
			Remove html from comment content

		Parameters:
			$content - Content

		Returns:
			String
	*/
	public function filterContentInput($content) {

		// remove all html tags or escape if in [code] tag
		$content = preg_replace_callback('/\[code\](.+?)\[\/code\]/is', create_function('$matches', 'return htmlspecialchars($matches[0]);'), $content);
		$content = strip_tags($content);

		return $content;
	}

	/*
		Function: filterContentOutput
			Auto linkify urls, emails

		Parameters:
			$content - Content

		Returns:
			String
	*/
	public function filterContentOutput($content) {

		$content = ' '.$content.' ';
	    $content = preg_replace_callback('/(?:(?:https?|ftp|file):\/\/|www\.|ftp\.)(?:\([-A-Z0-9+&@#\/%=~_|$?!:;,.]*\)|[-A-Z0-9+&@#\/%=~_|$?!:;,.])*(?:\([-A-Z0-9+&@#\/%=~_|$?!:;,.]*\)|[A-Z0-9+&@#\/%=~_|$])/ix', array($this->app->comment, 'makeURLClickable'), $content);
	    $content = preg_replace("/\s([a-zA-Z][a-zA-Z0-9\_\.\-]*[a-zA-Z]*\@[a-zA-Z][a-zA-Z0-9\_\.\-]*[a-zA-Z]{2,6})([\s|\.|\,])/i"," <a href=\"mailto:$1\" rel=\"nofollow\">$1</a>$2", $content);
		$content = $this->app->string->substr($content, 1);
		$content = $this->app->string->substr($content, 0, -1);

		return nl2br($content);
	}

	public function makeURLClickable($matches) {

		$url = $original_url = $matches[0];

		if (empty($url)) {
			return $url;
		}

		// Prepend scheme if URL appears to contain no scheme (unless a relative link starting with / or a php file).
		if (strpos($url, ':') === false &&	substr($url, 0, 1) != '/' && substr($url, 0, 1) != '#' && !preg_match('/^[a-z0-9-]+?\.php/i', $url)) {
			$url = 'http://' . $url;
		}

		return " <a href=\"$url\" rel=\"nofollow\">$original_url</a>";
	}

	/*
		Function: akismet
			Check if comment is spam using Akismet.

		Parameters:
			$comment - Comment
			$api_key - Akismet (Wordpress) API Key

		Returns:
			Void
	*/
	public function akismet($comment, $api_key = '') {

		// load akismet class
		$this->app->loader->register('Akismet', 'libraries:akismet/akismet.php');

		// check comment
		$akismet = new Akismet(JURI::root(), $api_key);
		$akismet->setCommentAuthor($comment->author);
		$akismet->setCommentAuthorEmail($comment->email);
		$akismet->setCommentAuthorURL($comment->url);
		$akismet->setCommentContent($comment->content);

		// set state
		if ($akismet->isCommentSpam()) {
			$comment->state = Comment::STATE_SPAM;
		}

	}

	/*
		Function: mollom
			Check if comment is spam using Mollom.

		Parameters:
			$comment - Comment
			$public_key - Public Key
			$private_key - Private Key

		Returns:
			Void
	*/
	public function mollom($comment, $public_key = '', $private_key = '') {

		// check if curl functions are available
		if (!function_exists('curl_init')) return;

		// load mollom class
		$this->app->loader->register('Mollom', 'libraries:mollom/mollom.php');

		// set keys and get servers
		Mollom::setPublicKey($public_key);
		Mollom::setPrivateKey($private_key);
		Mollom::setServerList(Mollom::getServerList());

		// check comment
		$feedback = Mollom::checkContent(null, null, $comment->content, $comment->author, $comment->url, $comment->email);

		// set state
		if ($feedback['spam'] != 'ham') {
			$comment->state = Comment::STATE_SPAM;
		}

	}

	/*
		Function: sendNotificationMail
			Send notification email

		Parameters:
			$comment - Comment
			$recipients - Array email => name
			$layout - The layout

		Returns:
			Void
	*/
	public function sendNotificationMail($comment, $recipients, $layout) {

		// workaround to make sure JSite is loaded
		$this->app->loader->register('JSite', 'root:includes/application.php');

		// init vars
		$item			  = $comment->getItem();
		$website_name	  = $this->app->system->application->getCfg('sitename');
		$comment_link	  = JRoute::_($this->app->route->comment($comment, false), true, -1);
		$item_link		  = JRoute::_($this->app->route->item($item, false), true, -1);
		$website_link	  = JRoute::_('index.php', true, -1);

		// send email to $recipients
		foreach ($recipients as $email => $name) {

			if (empty($email) || $email == $comment->getAuthor()->email) {
				continue;
			}

			// build unsubscribe link
			$unsubscribe_link = JURI::root().'index.php?'.http_build_query(array(
				'option' => $this->app->component->self->name,
				'controller' => 'comment',
				'task' => 'unsubscribe',
				'item_id' => $item->id,
				'email' => urldecode($email),
				'hash' => $this->app->comment->getCookieHash($email, $item->id, '')
			), '', '&');

			$mail = $this->app->mail->create();
			$mail->setSubject(JText::_("Topic reply notification")." - ".$item->name);
			$mail->setBodyFromTemplate($item->getApplication()->getTemplate()->resource.$layout, compact(
				'item',
				'comment',
				'website_name',
				'email',
				'name',
				'comment_link',
				'item_link',
				'website_link',
				'unsubscribe_link'
			));
			$mail->addRecipient($email);
			$mail->Send();
		}
	}

}

/*
	Class: CommentHelperException
*/
class CommentHelperException extends AppException {}