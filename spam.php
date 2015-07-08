<?php
/**
 * Spam filtering
 */

function sssb_stop_forum_spam_api( $args = array() ) {
	$defaults = array(
		'ip' 			=> ( isset( $_SERVER['REMOTE_ADDR'] ) ) ? $_SERVER['REMOTE_ADDR'] : '',
		'email'			=> '',
		'username'		=> ''
	);
	$url = 'http://www.stopforumspam.com/api?';
	$args = wp_parse_args( $args, $defaults );
	$args['f']	= 'json';
	$args['confidence']	= true;
	$args = array_filter( $args );
	$query = $url . http_build_query( $args );
	$key = md5( $query );
	if( false === ( $transient = get_transient( 'sssb_spam_' . $key ) ) ) {
		$result = wp_remote_get( $query );
		if( ! is_wp_error( $result ) ) {

			if( strlen( $result['body'] ) < 10 || ! $result['response']['code'] == 200 ) {
				return false;
			}

			if( $data = json_decode( $result['body'] ) ) {
				// it is json. continue
				if( $data->success != 1 ) {
					return false;
				}

				if( isset( $data->ip ) || isset( $data->email ) || isset( $data->username ) ) {
					
					$blocked = false;
					
					if( isset( $data->ip->confidence ) && $data->ip->confidence > get_option( 'sssb_confidence_ip', 75 ) ) { $blocked = 'ip'; }
					if( isset( $data->username->confidence ) && $data->username->confidence > get_option( 'sssb_confidence_username', 80 ) ) { $blocked = 'username'; }
					if( isset( $data->email->confidence ) && $data->email->confidence > get_option( 'sssb_confidence_email', 75 ) ) { $blocked = 'email'; }

					if( $blocked ) {
						$event = array(
							't'	=> 'event',
							'ec'	=> 'scheduled',
							'ea'	=> 'spam_blocked_' . $blocked,
							'el'	=> 0
						);
						if( isset( $events['weekly'][ $event['ea'] ] ) ) {
							$events['weekly'][ $event['ea'] ]['el']++;
						} else {
							$events['weekly'][ $event['ea'] ] = $event;
						}
						update_option( 'sssb_cron', $events );
						set_transient( 'sssb_spam_' . $key, 'yes', DAY_IN_SECONDS );
						sssb_update_spam_count();
						return true;
					} else {
						set_transient( 'sssb_spam_' . $key, 'no', DAY_IN_SECONDS );
					}
				}
			}
		}
	} else {
		if( 'yes' === $transient ) {
			sssb_update_spam_count();
			return true;
		} else {
			return false;
		}		
	}
	return false;
}

//check ip on login pageload
function sssb_spam_check_ip_init() {
	if( sssb_stop_forum_spam_api() ) {
		wp_die( '<center>Your IP is on a <a href="http://stopforumspam.com">Spam Blacklist</a>.</center>', 'Seriously Simple Spam Blocker' );
	}
}
add_action( 'plugins_loaded', 'sssb_spam_check_ip_init' );

function sssb_spam_check_comment( $approved, $comment ) {

	if( ! empty( $comment['user_ID'] ) && get_user_by( 'id', $comment['user_ID'] ) ) {
		return $approved;
	}

	$check = array( 'ip' => $comment['comment_author_IP'] );
	
	if( isset( $comment['comment_author_email'] ) && is_email( $comment['comment_author_email'] ) ) {
		$check['email'] = $comment['comment_author_email'];
	}

	if( isset( $comment['comment_author'] ) ) {
		$check['username'] = $comment['comment_author'];
	}

	if( sssb_stop_forum_spam_api( $check ) ) {
		return 'spam';
	} else {
		return $approved;
	}

}
add_action( 'pre_comment_approved' , 'sssb_spam_check_comment', 99, 2 );

function sssb_add_blacklist_words( $words ) {
	if( isset( $_SERVER['PHP_SELF'] ) && strpos( $_SERVER['PHP_SELF'], '/options' ) || isset( $_SERVER['SCRIPT_NAME'] ) && strpos( $_SERVER['SCRIPT_NAME'], '/options' ) ) {
		return $words;
	}
	$words = explode( "\n", $words );
	$blocked_words = array( 'byob','poze','bdsm','paxil','cialis','incest','ambien','adipex','shemale','meridia','cumshot','adderall','hair-loss','bllogspot','hydrocodone','discreetordering','aceteminophen','augmentation','enhancement','phentermine','doxycycline','citalopram','cephalaxin','vicoprofen','lorazepam','oxycontin','oxycodone','percocet','tramadol','cymbalta','lesbian','lexapro','valtrex','titties','meridia','levitra','vicodin','ephedra','lipitor','breast','cyclen','viagra','valium','hqtube','ultram','clomid','vioxx','zolus','pussy','porno','xanax','penis','porn','dick','cock','tits','fuck','shit','gdf','gds' );
	$words = array_merge( $words, $blocked_words );
	$words = array_unique( $words );
	$words = implode( "\n", $words );
	return $words;
}
add_filter( 'option_blacklist_keys', 'sssb_add_blacklist_words' );

function sssb_add_moderation_words( $words ) {
	if( isset( $_SERVER['PHP_SELF'] ) && strpos( $_SERVER['PHP_SELF'], '/options' ) || isset( $_SERVER['SCRIPT_NAME'] ) && strpos( $_SERVER['SCRIPT_NAME'], '/options' ) ) {
		return $words;
	}
	$words = explode( "\n", $words );
	$moderated_words = array( 'д','и','ж','Ч','Б','[url=','[/url]','naked','sex','bitch','soma','gay','nude' );
	$words = array_merge( $words, $moderated_words );
	$words = array_unique( $words );
	$words = implode( "\n", $words );
	return $words;
}
add_filter( 'option_moderation_keys', 'sssb_add_moderation_words' );

function sssb_update_spam_count() {
	$blocked_num = get_option( 'sssb_blocked', 0 );
	$blocked_num++;
	update_option( 'sssb_blocked', $blocked_num );
}
