<?php
namespace core;
/**
 *
 * @author hrodicus@gmail.com
 */
class Mail
{
    
    /**
     * Send HTML mail
     * @param type $from
     * @param type $to
     * @param type $subject
     * @param type $message 
     */
    public static function send($from, $to, $subject, $message, $cc=false, $bcc=false, $html=true)
    {
        if(is_array($to))
        {
            $to = implode(', ', $to);
        }
        $headers='';
        if($html)
        {
            $headers  = 'MIME-Version: 1.0' . "\r\n";
            $headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
        }
        $headers .= "From: $from <$from>\r\n";
        if($cc)
            $headers .= "Cc: $cc\r\n";
        if($bcc)
            $headers .= "Bcc: $cc\r\n";

        return mail($to, $subject, $message, $headers);
    }
   
}