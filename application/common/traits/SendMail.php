<?php
/**
 * Created by PhpStorm.
 * User: qiangbi
 * Date: 17-6-12
 * Time: 上午9:44
 */

namespace app\common\traits;

use think\Config;
use PHPMailer\PHPMailer\PHPMailer;
use app\common\model\SystemConfig;

trait SendMail
{

    /**
     * phpmailer工具发送邮件
     * @param $sendUser 发送者账号
     * @param $sendpwd  发送者密码
     * @param $subject  标题
     * @param $toUser   接收用户
     * @param $sendName 发送者显示名称
     * @param $sendBody 发送内容
     * @return array
     */
    //TODO oldfunction
    public function phpmailerSend($sendUser, $sendpwd, $host, $subject, $toUser, $sendBody, $fromname)
    {
        $mail = new PHPMailer();
        $mail->IsSmtp(true);                         // 设置使用 SMTP
        $mail->Host = $host;       // 指定的 SMTP 服务器地址
        $mail->SMTPAuth = true;                  // 设置为安全验证方式
        $mail->Username = $sendUser; // SMTP 发邮件人的用户名
        $mail->Password = $sendpwd;            // SMTP 密码
        $mail->From = $sendUser;
        $mail->FromName = $fromname;
        $mail->CharSet = "UTF-8";
        $sendmail = Config::get('smsend.sendmail');
        $sendname = Config::get('smsend.sendname');
        $mail->AddReplyTo($sendmail, $sendname);//回复给谁
        $mail->AddAddress($toUser);
        //发送到谁 写谁$mailaddress
        $mail->WordWrap = 50;                // set word wrap to 50 characters
        $mail->IsHTML(true);                    // 设置邮件格式为 HTML
        $mail->Subject = $subject; //邮件主题// 标题
        $mail->Body = $sendBody;              // 内容
        $msg = $mail->Send();
        if (!$msg) {
            return false;
        } else {
            return true;
        }
    }


    /**
     * 获取support邮箱帐号
     * @return array|bool
     */
    //TODO oldfunction
    public function getEmailAccount()
    {
        //support邮箱
        $email = (new SystemConfig())->where(["name" => "SYSTEM_EMAIL", "need_auth" => 1])->find();
        if (!isset($email->value)) {
            return false;
        }
        //support密码
        $password = SystemConfig::where(["name" => "SYSTEM_EMAIL_PASSWORD", "need_auth" => 1])->find();
        if (!isset($password->value)) {
            return false;
        }
        //support host
        $host = SystemConfig::where(["name" => "SYSTEM_EMAIL_SMTPHOST", "need_auth" => 1])->find();
        if (!isset($host->value)) {
            return false;
        }
        return [
            "email" => $email->value,
            "password" => $password->value,
            "host" => $host->value
        ];
    }

}