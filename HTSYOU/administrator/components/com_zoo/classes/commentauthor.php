<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

/*
	Class: CommentAuthor
		Author related attributes and functions.
*/
class CommentAuthor {

	public $app;
	public $name;
	public $email;
	public $url;
	public $user_id;

	/*
    	Function: __construct
    	  Default Constructor

		Parameters:
	      $name - Author name
	      $email - Email address
	      $url - Homepage url
	      $user_id - Joomla user id

 	*/
	public function __construct($name = '', $email = '', $url = '', $user_id = '') {

		// set vars
		$this->name    = $name;
		$this->email   = $email;
		$this->url     = $url;
		$this->user_id = $user_id;
	}

	public function getAvatar($size = 32) {
		$default = JURI::root().'media/zoo/assets/images/avatar.png';

		if ($this->email) {
			return '<img title="'.$this->name.'" src="http://www.gravatar.com/avatar/'.md5($this->app->string->strtolower($this->email)).'?s='.$size.'&amp;d='.$default.'" height="'.$size.'" width="'.$size.'" alt="'.$this->name.'" />';
		} else {
			return '<img title="'.$this->name.'" src="'.$default.'" height="'.$size.'" width="'.$size.'" alt="'.$this->name.'" />';
		}
	}

	public function isGuest() {
		return empty($this->user_id);
	}

	public function isJoomlaAdmin() {
		return false;
	}

	public function getUserType() {
		return strtolower(str_replace('CommentAuthor', '', get_class($this)));
	}

}

/*
	Class: CommentAuthorJoomla
		Joomla Author related attributes and functions.
*/
class CommentAuthorJoomla extends CommentAuthor {

	public function getJoomlaUser() {
		return $this->app->user->get($this->user_id);
	}

	public function isJoomlaAdmin() {
		if ($user = $this->getJoomlaUser()) {
			return $this->app->user->isJoomlaAdmin($user);
		}
		return false;
	}

}

/*
	Class: CommentAuthorFacebook
		Facebook Author related attributes and functions.
*/
class CommentAuthorFacebook extends CommentAuthor {

	public function getAvatar($size = 32) {

		if ($this->user_id) {

			$cache 		 = $this->app->cache->create($this->app->path->path('cache:') . '/author_cache', true, 604800);
			$cache_check = ($cache) ? $cache->check() : false;
			$url 		 = '';

			// try to get avatar url from cache
			if ($cache_check) {
				$url = $cache->get($this->user_id);
			}

			// if url is empty, try to get avatar url from twitter
			if (empty($url)) {
				$info = $this->app->facebook->fields($this->user_id, array('picture'));
				if (isset($info['picture'])) {
					$url = $info['picture'];
				}
				if ($cache_check) {
					$cache->set($this->user_id, $url);
					$cache->save();
				}
			}

			if (!empty($url)) {
				return '<img alt="'.$this->name.'" title="'.$this->name.'" src="'.$url.'" height="'.$size.'" width="'.$size.'" />';
			}

		}
	    return parent::getAvatar($size);
	}

}

/*
	Class: CommentAuthorTwitter
		Twitter Author related attributes and functions.
*/
class CommentAuthorTwitter extends CommentAuthor {

	public function getAvatar($size = 32) {
		if ($this->user_id) {

			$cache 		 = $this->app->cache->create($this->app->path->path('cache:') . '/author_cache', true, 604800);
			$cache_check = ($cache) ? $cache->check() : false;
			$url 		 = '';

			// try to get avatar url from cache
			if ($cache_check) {
				$url = $cache->get($this->user_id);
			}

			// if url is empty, try to get avatar url from twitter
			if (empty($url)) {
				$info = $this->app->twitter->fields($this->user_id, array('profile_image_url'));
				if (isset($info['profile_image_url'])) {
					$url = $info['profile_image_url'];
				}
				if ($cache_check) {
					$cache->set($this->user_id, $url);
					$cache->save();
				}
			}

			if (!empty($url)) {
				return '<img alt="'.$this->name.'" title="'.$this->name.'" src="'.$url.'" height="'.$size.'" width="'.$size.'" />';
			}

		}
	    return parent::getAvatar($size);
	}

}