<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class Notify extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public static function curl_work($url = '', $parameter = '') 
	{     
		$curl_options = array( 
			CURLOPT_URL => $url,         
			CURLOPT_HEADER => false,         
			CURLOPT_RETURNTRANSFER => true,         
			CURLOPT_USERAGENT => 'Google Bot',         
			CURLOPT_FOLLOWLOCATION => true,         
			CURLOPT_SSL_VERIFYPEER => FALSE,         
			CURLOPT_SSL_VERIFYHOST => FALSE,         
			CURLOPT_POST => '1',         
			CURLOPT_POSTFIELDS => $parameter     
		);     
		$ch = curl_init(); 
    	curl_setopt_array($ch, $curl_options);     
		$result = curl_exec($ch);
		$retcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);     
		$curl_error = curl_errno($ch);     
		curl_close($ch);     
		$return_info = array(         
			'url' => $url,         
			'sent_parameter' => $parameter,         
			'http_status' => $retcode,         
			'curl_error_no' => $curl_error,         
			'web_info' => $result     
		);     
		return $return_info; 
	}
	
	/**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public static function via($url,$notice)
    {
       	if($url=='notify')
	   		$url=env('LOG_SLACK_WEBHOOK_NOTIFY_URL');
			
		$post_data = array(//post_data 欄位資料            
			 "payload" => json_encode(array('text'=> $notice))             
		);
		
		$post_data_str = http_build_query($post_data); 
		return Notify::curl_work($url, $post_data_str); //背景送出	 
    }

    
}
