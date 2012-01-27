<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

/*
	Class: CommentEvent
		Comment events.
*/
class CommentEvent {

	public static function init($event) {

		$comment = $event->getSubject();

	}

	public static function saved($event) {

		$comment = $event->getSubject();
		$app = $comment->app;

		if ($event['new']) {

			// init vars
			$params = $app->parameter->create($comment->getItem()->getApplication()->getParams()->get('global.comments.'));

			// send email to admins
			if ($comment->state != Comment::STATE_SPAM && ($recipients = $params->get('email_notification', ''))) {
				$app->comment->sendNotificationMail($comment, array_flip(explode(',', $recipients)), 'mail.comment.admin.php');
			}

			// send email notification to subscribers
			if ($comment->state == Comment::STATE_APPROVED && $params->get('email_reply_notification', false)) {
				$app->comment->sendNotificationMail($comment, $comment->getItem()->getSubscribers(), 'mail.comment.reply.php');
			}
		}

	}

	public static function deleted($event) {

		$comment = $event->getSubject();

	}

	public static function stateChanged($event) {

		$comment = $event->getSubject();
		$app = $comment->app;

		// send email notification to subscribers
		if ($comment->getItem()->getApplication()->getParams()->get('global.comments.email_reply_notification')) {
			if ($comment->state != $event['old_state'] && $comment->state == Comment::STATE_APPROVED) {
				$app->comment->sendNotificationMail($comment, $comment->getItem()->getSubscribers(), 'mail.comment.reply.php');
			}
		}

	}

}
