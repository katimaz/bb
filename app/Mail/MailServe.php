<?php
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Models\Setting;
use App\Mail\toMail;
use App\Mail\RegisterWelcome;
use Illuminate\Support\Facades\Mail;

class MailServe extends Mailable
{
    
	public function sendEmail($mailType='', $email='', $data=array()) {
		if(!count($data) || !$mailType || !$email)
			return false;
		$kind = $this->email_kind($mailType);
		$newText = $kind['body'];
		$titleText = $kind['title'];
		foreach($data as $key=>$value)
		{
			if($key=='btn')
			{
				$newText =  str_replace('<btn>','<a href="'.$value['url'].'" class="'.((isset($value['style']) && $value['style']=='button')?'btn':'line').'">'.$value['txt'].'</a>',$newText);
			}elseif($key=='btn1')
			{
				$newText =  str_replace('<btn1>','<a href="'.$value['url'].'" class="'.((isset($value['style']) && $value['style']=='button')?'btn':'line').'">'.$value['txt'].'</a>',$newText);
			}elseif($key=='btn2')
			{
				$newText =  str_replace('<btn2>','<a href="'.$value['url'].'" class="'.((isset($value['style']) && $value['style']=='button')?'btn':'line').'">'.$value['txt'].'</a>',$newText);
			}elseif($key=='btn3')
			{
				$newText =  str_replace('<btn3>','<a href="'.$value['url'].'" class="'.((isset($value['style']) && $value['style']=='button')?'btn':'line').'">'.$value['txt'].'</a>',$newText);
			}elseif($key=='array')
			{
				$array = $value;
				$aText = '';  
				foreach($array as $k => $v)
				{
					if(is_array($v))
					{
						$user = $v['user'];
						$aText .= '<br /><hr />好幫手 : <br /><table style="width:100%;"><tr><th rowspan="4" style="width:120px;text-align:left"><img src="'.url('/avatar/big/'.$user['usr_photo']).'" width="100" /></th></tr><tr><td>姓名 : '.$user['last_name'].''.$user['first_name'].'</td></tr><tr><td>電話 : '.$user['phone_number'].'</td></tr><tr><td>Email : '.$user['email'].'</td></tr></table><hr /><br />';	
					}else	
						$aText .= $k.' : '.$v.'<br />';	
				}
				$newText =  str_replace('<array>',$aText,$newText);
				//$kind['body'] =  str_replace('<array>','<a href="'.$value.'">'.$key.'</a>',$kind['body']);
			}else
			{
				$newText =  str_replace('<'.$key.'>', (($key!='name')?'〈'.$value.'〉':$value), $newText);
				$titleText =  str_replace('<'.$key.'>', (($key!='name')?'〈'.$value.'〉':$value), $titleText);
			}
		}
		Mail::to($email)->queue(new toMail($titleText,$newText));
			
	}
	
	public function email_kind($kind) {
      $setting = Setting::first();
	  $data = array(
		  '1-002'=>array('title'=>(($setting->welcome_email_subj)?$setting->welcome_email_subj:'歡迎加入BounBang幫棒平台'),'body'=>'<name> 您好，<br /><br />'.(($setting->welcome_email_body)?nl2br($setting->welcome_email_body):'BounBang幫棒, 您的好幫手。').'<br /><br />'),
		  '1-003'=>array('title'=>'','body'=>'<name> 您好，<br /><br />幫您服務還能幫您賺現金? 這麼棒的事就在"幫棒"!!<br />無論您是消費者或者是好幫手，只要是您介紹進來的朋友，都會是您拓展業務的夥伴們，享有團隊收益5%的現金回饋! 回饋無上限!!<br /><br /><url>'),
		  '1-005'=>array('title'=>'邀請您加入BounBang幫棒家族, 期待您的加入','body'=>'<name> 您好，<br /><br />幫您服務還能幫您賺現金? 這麼棒的事就在"幫棒"!! 無論您是消費者或者是好幫手，只要是您介紹進來的朋友，都會是您拓展業務的夥伴們，享有團隊收益5%的現金回饋! 回饋無上限!!<br /><br /><url>'),
		  '1-006'=>array('title'=>'夥伴團隊加入確認','body'=>'<name> 您好，<br /><br />您所推薦的好友<name1>已經加入幫棒家族，並已成為幫棒的好幫手。<name> 已經成為您的夥伴團隊成員。<br /><br />透過BounBang幫棒，當您的夥伴完成服務需求，幫棒將回饋此次交易金額的5%給您。除了自身提供的服務，您將可以推薦您的夥伴給需要服務的客戶們，擴展您的服務業務範圍，並獲取利潤回饋。<br /><br />回饋無上限。夥伴愈多，回饋愈多!!'),
		   '1-007'=>array('title'=>(($setting->email_veri_subj)?$setting->email_veri_subj:'BounBang幫棒 - 會員註冊驗證信'),'body'=>(($setting->email_veri_body)?'<name> 您好，<br /><br />'.nl2br($setting->email_veri_body):'<name> 您好，<br /><br />歡迎加入BounBang幫棒家族。<br /><br />您正在進行電子郵件信箱設定，請盡快完成電子郵件信箱驗證。<br /><br />請在 24 小時內點擊網址完成驗證。').'<br /><br /><btn>'),
		  '1-008'=>array('title'=>(($setting->email_veri_comp_subj)?$setting->email_veri_comp_subj:'BounBang幫棒 – 您已完成註冊驗證信'),'body'=>(($setting->email_veri_comp_body)?'<name> 您好，<br /><br />'.$setting->email_veri_comp_body:'<name> 您好，<br /><br />歡迎加入BounBang幫棒家族，您已完成電子郵件信箱設定。<br /><br />歡迎您由此進入幫棒').'<br /><br /><btn>'),
		  '1-009'=>array('title'=>'BounBang幫棒- 帳號刪除通知','body'=>'<name> 您好，<br /><br />這份信件確認您的帳號<name> 經從BounBang幫棒系統裡刪除。感謝您對BounBang幫棒長期的支持與合作。我們歡迎您任何寶貴的建議或意見 - <btn>。<br /><br />最後，我們誠摯地邀請您繼續加入<btn1>。我們將不定期地發布各式主題的活動訊息與邀請。也歡迎您繼續給我們支持與鼓勵。'),
		  '1-010'=>array('title'=>'BounBang幫棒- 利潤回饋金<money>','body'=>'<name> 您好，<br /><br />您有新的利潤回饋金<money>。今年累積利潤回饋金已達到<money1>。<br /><br />當您的夥伴團隊完成服務需求，幫棒將回饋給您此次交易金額的5%作為利潤分享。除了自身提供的服務，您也可以推薦夥伴們給需要服務的客戶們，擴展您的服務業務範圍，並獲取利潤回饋。<br /><br />回饋無上限。夥伴愈多，回饋愈多!!<br /><br /><b><btn> | <btn1> | <btn2></b><br /><br />'),
		  '1-010-1'=>array('title'=>'BounBang幫棒- 您已申請領回利潤回饋金<money>','body'=>'<name> 您好，<br /><br />您已申請領回利潤回饋金<money>。 目前您在BounBang幫棒上的回饋金還有<money1>。<br /><br />依據BounBang幫棒平台的回饋金作業規定，我們將於每個月25日統一將您申請領回的回饋金轉入至您在藍新金流平台開設的帳戶，若您要領出這筆金額，請記得依照藍新金流的領出作業操作。<br /><br />回饋無上限。夥伴愈多，回饋愈多!!<br /><br /><b><btn> | <btn1> | <btn2></b><br /><br />'),
		  '1-010-2'=>array('title'=>'BounBang幫棒- 您的利潤回饋金<money>已轉入您的帳戶','body'=>'<name> 您好，<br /><br />您的利潤回饋金<mondy>已在<text>轉入到您在藍新金流平台開設的帳戶，若您要領出這筆金額，請記得依照藍新金流的領出作業操作。<br /><br />回饋無上限。夥伴愈多，回饋愈多!!<br /><br /><b><btn> | <btn1> | <btn2></b><br /><br />'),
		  '1-011'=>array('title'=>'BounBang幫棒 – 新增收款帳戶通知','body'=>'<name> 您好，<br /><br />歡迎使用BounBang幫棒。<br />您已透過BounBang幫棒新增藍新金流服務平台的帳戶資訊，您所提供的帳戶資訊將依據藍新金流平台的使用規定進行，<br />這裡為您新增的藍新金流平台帳戶資訊。<br /><br /><array><br /><br />'),
		  '1-012'=>array('title'=>'BounBang幫棒 – 忘記密碼','body'=>'<name> 您好，<br /><br />按一下下方的按鈕重設您BounBang幫棒帳戶的密碼。<br /><br /><btn><br /><br />此連結將從您收到此封電子郵件起 24 小時內有效。<br />如果您沒有提出此要求，請忽略此電子郵件。'),
		  '1-013'=>array('title'=>'BounBang交易電子發票明細','body'=>'<name> 您好，<br /><br />感謝您使用BounBang幫棒，<br />這是您上個月在BounBang幫棒的消費發票明細，發票明細如下:<br /><br /><text><br /><br />感謝您的的支持，若有其他問題，歡迎聯絡我們，BounBang幫棒將儘速為您服務。'),
		  '2-001'=>array('title'=>'BounBang幫棒- <action> -  〔訂單 – <status> - <no> <item> <money>〕','body'=>'<name> 您好，<br /><br /><action>  -  〔訂單 – <status> - <no>  <item> <money>〕<br /><br/>您可至 <btn>做查詢與管理<br /><br /><array>。
'),
		  '2-002'=>array('title'=>'BounBang幫棒 –  一日前預約服務提醒- 〔訂單-進行中-<no> <item> <money>〕','body'=>'<name> 您好，<br /><br />幫棒提醒您，您在明日<time>有預定服務 - 〔訂單-進行中-<no> <item> <money>〕。<br /><br />當服務結束時，好幫手將會邀請您做簽收。<br />請您使用此四位數完成服務碼做簽收: <number><br /></br />您可至 我的帳戶>我的訂單 做查詢與管理。<br /><br />下列為案件的有關訊息，同時也提供好幫手的連絡資訊做為緊急連絡的需要。我們鼓勵您使用訂單內建的訊息功能溝通，最為日後如有糾紛的解決依據。<br /><br /><array><br /><br /><btn><br /><br />'),
		  '2-003'=>array('title'=>'BounBang幫棒 – 服務鈴(配對)送出通知 - 〔<ID> , <item> <money>〕','body'=>'<name> 您好，<br /><br />您的服務鈴(配對)工作已刊登 - 〔<ID> , <item> <money>〕。<br />合適的好幫手們很快會跟您聯絡。<br />您也可到<btn>做查詢與管理。<br /><br /><array><br />'),
		  '2-004'=>array('title'=>'BounBang幫棒 – 服務鈴(配對)更改通知 - 〔<no> <item> <money>〕','body'=>'<name> 您好，<br /><br />您的服務鈴(配對)已經修改 - 〔<no> <item> <money>〕。 合適的好幫手們很快會跟您聯絡。<br />下列為完整的配對資訊，您也可到<btn>做管理。<br /><br /><array><br /><br />'),
		  '2-004-1'=>array('title'=>'BounBang幫棒好幫手 - 服務鈴(配對)更改通知 - 〔服務鈴(配對)更改- <ID> <item> <money>〕','body'=>'<name> 您好，<br /><br />通知您有興趣的服務鈴(配對)案件內容有更改- 〔服務鈴(配對)更改- <ID>, <item> <money>〕。<br />您可以到<btn>做最新案件的即時配對管理與聯絡。<br /><br /><array><br /><br />'),
		  '2-005'=>array('title'=>'BounBang幫棒 – 服務鈴(配對)取消通知- 〔服務鈴(配對)取消- <no> <item> <money>〕','body'=>'<name> 您好，<br /><br />您已取消服務鈴(配對)工作刊登 - 〔服務鈴(配對)取消- <no> <item> <money>〕。您可以到<btn>做查詢。<br />下列為完整的取消配對工作刊登資訊:<br />服務鈴(配對)內容<br /><br /><array><br /><br /><btn1><br /><br />'),
		  '2-006'=>array('title'=>'BounBang幫棒 - 服務鈴(配對)找到好幫手通知- 〔<no> <item> <money>〕','body'=>'<name> 您好，<br /><br />您的服務鈴(配對)找到好幫手- 〔<no> <item> <money>〕。<br /><br />好幫手: <好幫手簡介><br /><br />請您到管理<btn>做查詢與管理。<br /><br />如果您不想收到此通知，請到<btn1>中做訊息管理。'),
		  '2-007'=>array('title'=>'BounBang幫棒 - 服務鈴(配對)過期通知- 〔服務鈴(配對)過期- <day>, <no> <item> <money>〕','body'=>'<name> 您好，<br /><br />通知您此服務鈴(配對)刊登已超過七日過期 - 〔服務鈴(配對)過期- <no> <item> <money>〕。您可以到<btn>做查詢。<br /><br />如您還有此需求，建議您回到主選單再做服務預約與服務鈴(配對)登錄。未來如果有合適的好幫手，我們也會主動發送通知給您做需求配對。<br /><br />下列為此過期的配對工作刊登資訊:<br />服務鈴(配對)內容<br /><br /><array><br /><br />'),
		  '2-008'=>array('title'=>' BounBang幫棒- 雇用取消 -  〔我的需求 – 確認中 - <no> <item> <money>〕','body'=>'<name> 您好，<br /><br />您的雇用訂單 - 〔我的需求 – 確認中 - <no> <item> <money>〕已超過24小時未得到好幫手回覆，為保障您的權益，我們將取消此雇用訂單，若您還需要預訂服務，我們誠摯的建議您重新發出您的需求並選擇其他好幫手。<br /><br />由於雇用訂單已取消，您將不會被收費。您可至 <btn> 做查詢與管理
<br /><br /><array><br /><br /><btn1><br /><br />'),
		  '2-008-1'=>array('title'=>'BounBang幫棒好幫手- 您的新工作預約已取消 - 〔我的工作 – 確認中 - <no> <item> <money>〕','body'=>'<name> 您好，<br /><br />您的新工作預約訂單 - 〔我的工作 – 確認中 - <no> <item> <money>〕已超過24小時未得到您的回覆，依據BounBang幫棒使用規則，我們已取消此雇用訂單，並通知客戶。<br /><br />您可至 我的帳戶>切換到好幫手>我的工作做查詢與管理。<br>下列為取消的新工作預約資訊，<br />取消預約明細<br /><br /><array><br /><br /><btn1><br /><br />'),
		  '2-009'=>array('title'=>'BounBang幫棒-修改服務內容通知– 〔詢問中-<ID>, <item> <money>〕','body'=>'<name> 您好，<br /><br />您已修改服務內容– 〔詢問中-<ID>, <item> <money>〕 您可至 <btn> 做查詢與管理<br /><br /><array><br /><br /><btn1><br /><br />'),
		  '2-009-1'=>array('title'=>'BounBang幫棒好幫手- 修改服務內容通知– 〔詢問中- <ID>, <item> <money>〕','body'=>'<name> 您好，<br /><br />您有修改服務內容通知 – 〔詢問中- <ID>, <item> <money>〕<br />您可至 <btn> 做查詢與管理<br /><br /><array><br /><br /><btn1><br /><br />'),
		  '2-010'=>array('title'=>'BounBang幫棒- 雇用訂單送出 -  〔我的需求 – 確認中 - <no> <item> <money>〕','body'=>'<name> 您好，<br /><br />您的雇用訂單已經送出 - 〔我的需求 – 確認中 - <no> <item> <money>〕。<br />您暫時不會被收費。當好幫手接受雇用訂單時，我們將會以email做及時通知。如好幫手因故不能接受訂單，我們也會以email做及時的通知。<br />您可至 <btn> 做查詢與管理<br /><br /><array><br /><br /><btn1><br /><br />'),
		  '2-010-1'=>array('title'=>'BounBang幫棒好幫手- 您有新工作預約 - 〔我的工作 – 確認中 - <no> <item> <money>〕','body'=>'<name> 您好，<br /><br />您有新工作預約 - 〔我的工作 – 確認中 - <no> <item> <money>〕<br /><br />請做接單確認。您可至 <btn> 做查詢與管理。<br /><br />下列為完整的訂單資訊，<br />預約明細<br /><br /><array><br /><br /><btn1><br /><br />'),
		  '2-011'=>array('title'=>'BounBang幫棒好幫手- 您有新訊息- 〔我的工作-確認中-<no> <item> <money>〕','body'=>'<name> 您好，<br /><br />您可至 <btn> 做查詢與管理<br /><br />新訊息<br /><br /><array><br /><br /><btn1><br /><br />'),
		  '2-011-1'=>array('title'=>'BounBang幫棒好幫手- 您有新訊息- 〔訂單-進行中-<no> <item> <money>〕','body'=>'<name> 您好，<br /><br />您有來自客戶的新訊息- 〔訂單-進行中-<no> <item> <money>〕<br /><br />您可至 <btn> 做查詢與管理<br />新訊息<br /><br /><array><br /><br /><btn1><br /><br />'),
		  '2-012'=>array('title'=>'BounBang幫棒- 修改雇用內容通知 - 〔我的需求-確認中-<no> <item> <money>〕','body'=>'<name> 您好，<br /><br />您已更改雇用內容 - 〔我的需求-確認中-<no> <item> <money>〕。<br /><br />您可至 <btn> 做查詢與管理<br /><br />修改雇用內容<br /><br /><array><br /><br /><btn1><br /><br />'),
		  '2-012-1'=>array('title'=>'BounBang幫棒好幫手 - 修改工作預約內容通知 – 〔我的工作-確認中-XXXX, <no> <item> <money>〕','body'=>'<name> 您好，<br /><br />您的客戶修改工作預約內容 – 〔我的工作-確認中-XXXX, <no> <item> <money>〕<br /><br />您可至 <btn> 做查詢與管理<br /><br />修改工作預約內容<br /><br /><array><br /><br /><btn1><br /><br />'),
		  '2-013'=>array('title'=>'BounBang幫棒-取消雇用通知 - 〔我的需求-確認中-<no> <item> <money>〕','body'=>'<name> 您好，<br /><br />您已取消雇用 - 〔我的需求-確認中-<no> <item> <money>〕。<br /><br />您可至 <btn> 做查詢與管理<br /><br />下列為完整的取消資訊。<br /><br />取消雇用明細<br /><br /><array><br /><br /><btn1><br /><br />'),
		  '2-013-1'=>array('title'=>'BounBang幫棒好幫手-取消工作預約通知 - 〔我的工作-確認中-<no> <item> <money>〕','body'=>'<name> 您好，<br /><br />您的客戶已取消工作預約- 〔我的工作-確認中-<no> <item> <money>〕<br /><br />您可至 <btn> 做查詢與管理<br /><br />下列為完整的取消訂單資訊。<br /><b>取消工作預約</b><br /><br /><array><br /><br /><btn1><br /><br />'),
		  '2-014'=>array('title'=>'BounBang幫棒-更改訂單時間通知 – 〔訂單-進行中--<no> <item> <money>','body'=>'<name> 您好，<br /><br />您已更改訂單時間 – 〔訂單-進行中--<no> <item> <money>〕。<br /><br />我們已將您更改訂單時間的需求通知好幫手，並請好幫手確認您提出的更改需求。<br />您可至 我的帳戶>我的訂單 做查詢與管理<br /><br /><array><br /><br /><btn1><br /><br />'),
		  '2-014-1'=>array('title'=>'BounBang幫棒-更改訂單時間通知 – 〔訂單-確認中-<no> <item> <money>〕','body'=>'<name> 您好，<br /><br />您已更改訂單時間 - 〔訂單-確認中-<no> <item> <money>〕。<br /><br />更改服務手續費: &<money>。請參考相關取消，修改，與違約條款<br />我們將直接從您的信用卡中扣取費用。<br /><br />您可至 <btn> 做查詢與管理<br /><br /><array><br /><br /><btn1><br /><br />'),
		  '2-014-2'=>array('title'=>'BounBang幫棒好幫手 -更改訂單時間通知 – 〔訂單-確認中-<no> <item> <money>〕','body'=>'<name> 您好，<br /><br />您有來自客戶更改訂單時間 – 〔訂單-確認中-<no> <item> <money>〕。請您至 <btn> 內確認這筆訂單更改需求。<br /><br />您可至 <btn1> 做查詢與管理<br /><bt />更改訂單時間<br /><br /><array><br /><br /><btn1><br /><br />'),
		  '2-014-3'=>array('title'=>'BounBang幫棒-更改訂單時間已確認 – 〔訂單-進行中-<no> <item> <money>〕','body'=>'<name> 您好，<br /><br />好幫手已接受您提出的更改訂單時間 – 〔訂單-進行中-<no> <item> <money>〕。<br /><br />您可至 <btn> 做查詢與管理<br /><br />更改訂單時間<br /><br />您可至 <btn> 做查詢與管理<br /><br /><array><br /><br /><btn1><br /><br />'),
		  '2-014-4'=>array('title'=>'BounBang幫棒好幫手- 已接受更改訂單時間 – 〔訂單-確認中-<no> <item> <money>〕','body'=>'<name> 您好，<br /><br />您已接受客戶更改訂單時間 – 〔訂單-確認中-<no> <item> <money>〕。<br /><br />您可至 <btn> 做查詢與管理<br /><br />更改訂單時間<br /><br /><array><br /><br /><btn1><br /><br />'),
		  '2-014-5'=>array('title'=>'BounBang幫棒-更改訂單時間被拒絕 – 〔訂單-進行中-<no> <item> <money>〕','body'=>'<name> 您好，<br /><br />好幫手已拒絕您提出的更改訂單時間 – 〔訂單-進行中-<no> <item> <money>〕。<br /><br />您訂單中的服務時間將維持原訂時間，不會被更改。我們建議您可以與好幫手討論其他時段，若無法達成時間更改協議，您可保留原時段或者取消此訂單。<br /><br />您可至 <btn> 做查詢與管理<br /><br />更改訂單時間<br /><br /><array><br /><br /><btn1><br /><br />'),
		  '2-014-6'=>array('title'=>'BounBang幫棒好幫手- 已拒絕更改訂單時間 – 〔訂單-確認中-<no> <item> <money>〕','body'=>'<name> 您好，<br /><br />您已拒絕客戶更改訂單時間 – 〔訂單-確認中-<no> <item> <money>〕。<br /><br />這筆訂單的時間將不會更改，提醒您依照原約定時間為客戶提供服務。您可至 <btn> 做查詢與管理<br /><br />更改訂單時間<br /><br /><array><br /><br /><btn1><br /><br />'),
		  '2-014-7'=>array('title'=>'BounBang幫棒-更改訂單時間不被接受 – 〔訂單-進行中-<no> <item> <money>〕','body'=>'<name> 您好，<br /><br />好幫手已超過24小時未回覆您提出的更改訂單時間需求，此訂單時間更改需求已被取消 – 〔訂單-進行中-<no> <item> <money>〕。<br /><br />您訂單中的服務時間將維持原訂時間，不會被更改。我們建議您可以與好幫手討論其他時段，若無法達成時間更改協議，您可保留原時段或者取消此訂單。<br />您可至 <btn> 做查詢與管理<br /><br />更改訂單時間<br /><br /><array><br /><br /><btn1><br /><br />'),
		  '2-014-8'=>array('title'=>'BounBang幫棒好幫手 -已拒絕更改訂單時間 – 〔訂單-確認中-<no> <item> <money>〕','body'=>'<name> 您好，<br /><br />您已超過24小時未回覆客戶提出的更改訂單時間需求，此訂單時間更改需求已被取消 – 〔訂單-確認中-<no> <item> <money>〕。<br /><br />這筆訂單的時間將不會更改，提醒您依照原約定時間為客戶提供服務。<br /><br />您可至 我的帳戶>切換到好幫手>我的訂單做查詢與管理<br /><br />更改訂單時間<br /><br /><array><br /><br /><btn1><br /><br />'),
		  '2-015'=>array('title'=>'BounBang幫棒-訂單取消通知 – 〔訂單-進行中-<no> <item> <money>〕','body'=>'<name> 您好，<br /><br />您已取消進行中訂單– 〔訂單-進行中-<no> <item> <money>〕。<br /><br />取消服務手續費: <money1><br />訂單費用退款: 我們將扣除取消服務手續費，並於五個工作天內退還其餘的訂單費用至您所指定的帳號內。請參考相關的<btn1><br />您可至 <btn> 做查詢與管理<br /><br />下列為完整的訂單取消資訊。訂單明細<br /><br /><array><br /><br /><btn1><br /><br />'),
		  '2-015-1'=>array('title'=>'BounBang幫棒好幫手- 訂單取消通知 - 〔訂單-進行中-<no> <item> <money>〕','body'=>'<name> 您好，<br /><br />客戶已取消進行中的訂單- 〔訂單-進行中-<no> <item> <money>〕。<br /><br />您可至 <btn> 做查詢與管理<br /><br />下列為完整的訂單取消資訊。<br /><br />訂單明細<br /><br /><array><br /><br /><btn1><br /><br />'),
		  '2-016'=>array('title'=>'BounBang幫棒好幫手- 您有新訊息– 〔詢問中-<ID>, <item> <money>〕','body'=>'<name> 您好，<br /><br />您有新訊息– 〔詢問中-<ID>, <item> <money>〕<br /><br />您可至 <btn> 做查詢與管理<br /><br />新訊息<br /><br /><array><br /><br /><btn1><br /><br />'),
		  '2-017'=>array('title'=>'BounBang幫棒- 我們於期限內未收到您訂單的支付款項，訂單已取消 - 〔訂單-等待付款- <no> <item> <money>〕','body'=>'<name> 您好，<br /><br />您的訂單 - 〔訂單-等待付款- <no> <item> <money>〕<br /><br />我們於期限內收到您此筆訂單的款項，依據BounBang幫棒的使用規則，我們已取消此筆訂單，若您還需要預訂服務，我們誠摯的建議您重新發出您的需求。<br /><br />由於您未完成付款造成訂單取消，我們不會向您收取費用。<br /><br />取消訂單明細<br /><br /><array><br /><br /><btn1><br /><br />'),
		  '2-017-1'=>array('title'=>'BounBang幫棒- 我們於期限內未收到客戶支付款項，訂單已取消 - 〔訂單-等待付款- <no> <item> <money>〕','body'=>'<name> 您好，<br /><br />您的訂單 - 〔訂單-等待付款- <no> <item> <money>〕<br /><br />我們於期限內未收到客戶支付的款項，未保障您的權益，我們已取消此筆訂單，並通知客戶。<br /><br />您可至 我的帳戶>切換到好幫手>我的工作做查詢與管理。<br /><br />取消訂單明細<br /><br /><array><br /><br /><btn1><br /><br />'),
		  '2-017-2'=>array('title'=>'BounBang幫棒- 等待付款 - 〔訂單-等待付款- <no> <item> <money>〕','body'=>'<name> 您好，<br /><br />您的訂單 - 〔訂單-等待付款- <no> <item> <money>〕<br /><br />已完成上個階段。請於7日內付款下一階段的訂單。<br /><br />訂單明細<br /><br /><array><br /><br /><btn1><br /><br />'),
		  '2-017-3'=>array('title'=>'BounBang幫棒- 等待付款 - 〔訂單-等待付款- <no> <item> <money>〕','body'=>'<name> 您好，<br /><br />您的訂單 - 〔訂單-等待付款- <no> <item> <money>〕<br /><br />已完成上一階段。好幫手有七日決定是否往下一階段進行。當好幫手付款下一階段，我們將通知您。<br /><br />您可至 我的帳戶>切換到好幫手>我的工作做查詢與管理。<br /><br />訂單明細<br /><br /><array><br /><br /><btn1><br /><br />'),
		  '2-018'=>array('title'=>'BounBang幫棒- 我們已收到您訂單的支付款項 - 〔訂單-等待付款- <no> <item> <money>〕','body'=>'<name> 您好，<br /><br />您的訂單 - 〔訂單-等待付款- <no> <item> <money>〕。<br /><br />謝謝您!我們已收到您此筆訂單的款項。<br />您可至 <btn> 做查詢與管理。<br /><br />訂單明細<br /><br /><array><br /><br /><btn1><br /><br />'),
		  '2-018-1'=>array('title'=>'BounBang幫棒- 我們已收到客戶支付款項 - 〔訂單-等待付款- <no> <item> <money>〕','body'=>'<name> 您好，<br /><br />您的訂單 - 〔訂單-等待付款- <no> <item> <money>〕。<br /><br />我們已收到客戶支付的款項，提醒您於約定的時間完成與客戶議定的服務。<br>您可至 <btn> 做查詢與管理。<br /><br />訂單明細<br /><br /><array><br /><br /><btn1><br /><br />'),
		  '2-019'=>array('title'=>'BounBang幫棒- 您的信用卡扣款未成功，請您完成款項支付 - 〔訂單-等待付款- <no> <item> <money>〕','body'=>'<name> 您好，<br /><br />您的訂單 - 〔訂單-等待付款- <no> <item> <money>〕。<br /><br />由於您提供的信用卡未能成功扣款，請您按此支付您的訂單款項，以免影響您預訂服務的權益。<br /><br />您可至 我的訂單 做查詢與款項支付。<br /><br />訂單明細<br /><br /><array><br /><br /><btn1><btn2><br /><br />'),
		  '2-020'=>array('title'=>'BounBang幫棒- 您於服務約定時間未出席，訂單已取消 - 〔訂單-進行中- <no> <item> <money>〕','body'=>'<name> 您好，<br /><br />您的訂單 - 〔訂單-進行中- <no> <item> <money>〕。好幫手於約定時間<date>在約定訂點<text>未等到您的出席而致使好幫手無法提供服務，BounBang幫棒將依據平台使用規則將此筆訂單取消並向您收取30%的訂單金額作為取消訂單的訂金，剩餘款項將依據收付款規定退回您的帳戶。<br /><br />
您可至 我的訂單 做查詢與管理。<br /><br />取消訂單明細<br /><br /><array><br /><br /><btn1><btn2><br /><br />'),
		  '2-020-1'=>array('title'=>'BounBang幫棒- 您於服務約定時間未出席，訂單已取消 - 〔訂單-進行中- <no> <item> <money>〕','body'=>'<name> 您好，<br /><br />您的訂單 - 〔訂單-進行中- <no> <item> <money>〕。客戶於約定時間<date>在約定訂點<text>未等到您的出席而致使無法使用您提供服務，BounBang幫棒將依據平台使用規則將此筆訂單取消並向您收取30%的訂單金額作為取消訂單的訂金，請您至 我的帳務>支出完成訂金支付，若未完成支付，將會影響您在BounBang幫棒平台上的使用權益。<br /><br />您可至 我的訂單 做查詢與管理以及至 我的帳務>支出 完成相關款項支付。<br /><br />取消訂單明細<br /><br /><array><br /><br /><btn1><btn2><br /><br />'),
		  '2-021'=>array('title'=>'<name1> 推薦您BounBang幫棒的服務','body'=>'<name> 您好，<br /><br /><name1> 推薦您BounBang幫棒的服務。<br /><br /><array>一鍵雇用<br />您可至 '.url('/').' 做詳細服務內容查詢。<br /><br />'),
		  '2-021-1'=>array('title'=>'推薦幫棒服務給朋友 – FB/Line','body'=>'<name> 您好，<br /><br /><name1> 推薦您BounBang幫棒的服務。<br /><br /><array>一鍵雇用<br />您可至 '.url('/').' 做詳細服務內容查詢。<br /><br />'),
		  '3-001'=>array('title'=>'BounBang幫棒好幫手- <action> -  〔訂單 – <status> - <no>  <item> <money>〕','body'=>'<name> 您好，<br /><br /><title>  -  〔訂單 – <status> - <no>  <item> <money>〕<br /><br />您可至 我的帳戶>切換到好幫手> 我的工作列 做查詢與管理。<br /><br /><title><br /><br /><array><br /><br />'),
		  '3-002'=>array('title'=>'BounBang幫棒- 您有新訊息– 〔詢問中--<ID> <item> <money>〕','body'=>'<name> 您好，<br /><br />您有新訊息– 〔詢問中--<ID> <item> <money>〕<br /><br />您可至 我的帳戶>管理我的需求做查詢與管理<br /><br />新訊息<br /><br /><array><br /><br /><btn> | <btn1> | <btn2><br /><br />如果您不想收到此通知，請到通知設定中做訊息管理。<br /><br />'),
		  '3-004'=>array('title'=>'BounBang幫棒- 您有新訊息-〔我的需求-確認中-<no> <item> <money>〕','body'=>'<name> 您好，<br /><br />您有來自好幫手的訊息-〔我的需求-確認中-<no> <item> <money>〕<br /><br />您可至 我的帳戶>我的需求 做查詢與管理<br /><br />新訊息<br /><br /><array><br /><br /><btn> | <btn1><br /><br />'),
		  '3-005'=>array('title'=>'BounBang幫棒- 雇用訂單確認通知 - 〔訂單-進行中- <no> <item> <money>〕','body'=>'<name> 您好，<br /><br />您的雇用訂單 - 〔訂單-進行中- <no> <item> <money>〕已經確認。<br /><br />我們將直接從您的信用卡中扣取費用。<br /><br />訂單明細<br /><br /><array><br /><br /><btn> | <btn1><br /><br />'),
		  '3-005-1'=>array('title'=>'BounBang幫棒好幫手- 訂單確認通知 - 〔訂單-進行中-<no> <item> <money>〕','body'=>'<name> 您好，<br /><br />您已確認訂單〔訂單-進行中-<no> <item> <money>〕。<br /><br />訂單明細<br /><br /><array><br /><br /><btn> | <btn1><br /><br />'),
		  '3-006'=>array('title'=>'BounBang幫棒-雇用訂單拒絕通知- 〔我的需求-確認中-<no> <item> <money>〕','body'=>'<name> 您好，<br /><br />好幫手沒辦法接雇用訂單。您的預約已取消 - 〔我的需求-確認中-<no> <item> <money>〕。<br /><br /><好幫手推薦<btn>來幫您。> 您<也>可以選擇找尋其他的好幫手來幫您。<br /><br />下列為完整的預約取消資訊。<br /><br />預約明細<br /><br /><array><br /><br /><btn> | <btn1><br /><br />'),
		  '3-006-1'=>array('title'=>'BounBang幫棒好幫手- 預約拒絕通知-〔我的工作-確認中-<no> <item> <money>〕','body'=>'<name> 您好，<br /><br />您已拒絕預約〔我的工作-確認中-<no> <item> <money>〕。<br /><br />下列為完整的預約取消資訊。<br /><br />預約明細<br /><br /><array><br /><br /><btn> | <btn1><br /><br />'),
		  '3-008'=>array('title'=>'BounBang幫棒- 您有新訊息 - 〔訂單-進行中-<no> <item> <money>〕','body'=>'<name> 您好，<br /><br />您有新訊息 - 〔訂單-進行中-<no> <item> <money>〕<br /><br />您可至 我的帳戶>我的需求做查詢與管理<br /><br />新訊息<br /><br /><array><br /><br /><btn> | <btn1><br /><br />'),
		  '3-010'=>array('title'=>'BounBang幫棒-訂單取消通知 - 〔訂單-進行中-<no> <item> <money>〕','body'=>'<name> 您好，<br /><br />好幫手已取消訂單: 〔訂單-進行中-<no> <item> <money>〕。<br /><br />請重新找尋新的好幫手來幫您。<br /><br />訂單費用退款:我們將於五個工作天內退還訂單費用至您所指定的帳號內。請參考相關的訂單退款條約<br />您可至 我的帳戶>管理我的需求 做查詢與管理<br /><br />下列為完整的訂單取消資訊。<br /><br />訂單明細<br /><br /><array><br /><br /><btn> | <btn1><br /><br />'),
		  '3-010-1'=>array('title'=>'BounBang幫棒好幫手- 您已取消訂單- 〔訂單-進行中-<no> <item> <money>〕','body'=>'<name> 您好，<br /><br />您已取消〔訂單-進行中-<no> <item> <money>〕。<br /><br />取消服務手續費: <money1><br /><br />根據相關的訂單退款條約，您被收取取消服務手續費<money1>.請盡速繳款。<br /><br />下列為已取消的服務:<br /><br />訂單明細<br /><br /><array><br /><br /><btn> | <btn1><br /><br />'),
		  '3-011'=>array('title'=>'BounBang幫棒- 訂單完成確認通知 – 〔訂單-確認中-<no> <item> <money>〕','body'=>'<name> 您好，<br /><br />您的訂單- 〔訂單-確認中-<no> <item> <money>〕 <br /><br />已經於<day><week><time>完成。請於24小時內做服務的確認。如果您超過24小時對此服務沒做確認或提除異議，我們將自動確認此訂單並把服務相關的收費付給好幫手。<br /><br />下列為已完成的服務:<br />訂單內容<br />訂單: <no><br /><br />好幫手:<br /><br /><array><br /><br /><btn><br /><br />'),
		  '3-012'=>array('title'=>'BounBang幫棒- 訂單完成通知','body'=>'<name> 您好，<br /><br />您的訂單- 〔訂單-確認中-<no> <item> <money>〕 <br /><br />已經於<day><week><time>完成。若您很滿意此次的服務，您可以做下次的服務預定，或將好幫手加入您的首選。<br /><br />下列為已完成的服務:<br />訂單內容<br />訂單: <no><br /><br />好幫手:<br /><br /><array><br /><br /><btn><br /><br />感謝您寶貴的意見，祝您有個美好的一天。<br /><br />'),
		  '3-014'=>array('title'=>'BounBang幫棒好幫手 – 最新服務鈴(配對)通知 - 〔服務鈴(配對)- <ID>, <item> <money>〕','body'=>'<name> 您好，<br /><br />這是您所訂閱的最新服務鈴(配對)通知- 〔服務鈴(配對) - <ID>, <item> <money>〕。 <br /><br />已依您設定的類別【<class>】，寄送合適案件給您。您也可以到工作管理– 配對做最新案件的即時配對管理與聯絡。<br /><br />服務鈴(配對)內容<br /><br /><array><br /><br /><btn> | <btn1><br /><br />'),
		  '3-016'=>array('title'=>'BounBang幫棒好幫手- 一日前預約服務提醒-〔進行中-<no> <item> <money>〕','body'=>'<name> 您好，<br /><br />幫棒提醒您，您在<date>有預定服務-〔進行中-<no> <item> <money>〕，請於預定時間完成訂單。<br /><br />當服務完成時，請回到我的訂單做服務完成確認，並請客戶輸入完成服務確認碼做簽收認證。<br /><br />您可至 我的帳戶>切換到好幫手> 我的訂單 做查詢與管理。<br />下列為案件的有關訊息，同時提供客戶的連絡資訊做為緊急連絡的需要。我們鼓勵您使用訂單內建的訊息功能溝通，最為日後如有糾紛的解決依據。<br /><br />案件內容<br /><br /><array><br /><br /><btn><br /><br />'),
		  '3-018'=>array('title'=>'BounBang幫棒好幫手- 提醒您做訂單完成確認 - 〔訂單-確認中-<no> <item> <money>〕','body'=>'<name> 您好，<br /><br />您已經於<date> <week><time>完成訂單- 〔訂單-確認中-<no> <item> <money>〕。<br />請盡速至我的帳戶>切換到好幫手>我的訂單做訂單完成確認以確保您的服務收費。<br /><br />下列為服務信息:<br />訂單內容<br /><br /><array><br /><br /><btn><br /><br />'),
		  '3-019'=>array('title'=>'BounBang幫棒好幫手- 訂單完成通知','body'=>'<name> 您好，<br /><br />客戶已確認您已經於<day> <week><time>完成訂單- 〔訂單-確認中-<no> <item> <money>〕。<br /><br />幫棒將於五個工作天內將訂單金額扣除平台費用，匯入您的戶頭中。<br /><br />下列為已完成的服務:<br />訂單內容<br /><br /><array><br /><br /><btn1><br /><br />'),
		  '3-020'=>array('title'=>'BounBang幫棒好幫手- 服務收入入帳通知- 〔訂單-完成-<no> <item> <money>〕','body'=>'<name> 您好，<br /><br />您的訂單 - 〔訂單-完成-<no> <item> <money>〕- 的服務收入已入帳<money>(已扣除平台服務費及藍新金流第三方交易手續費)。<br /><br />年度累計收入:<br />年度累積服務收入已達到<money1>。年度累積利潤回饋金已達到<money2>。<br /><br />推薦賺回饋:<br />當您的夥伴團隊完成服務需求，幫棒將回饋給您此次交易金額的5%作為利潤分享。<br />除了自身提供的服務外，您也可以推薦您的夥伴給需要服務的客戶們，擴展您的服務業務範圍，並獲取利潤回饋。<br />回饋無上限。夥伴愈多，回饋愈多!!<br /><br /><array><br /><br /><btn1><br /><br />'),
		  '3-021'=>array('title'=>'BounBang幫棒好幫手 – 好幫手個人資料審核中','body'=>'<name> 您好，<br /><br />歡迎您加入BounBang幫棒 好幫手。<br /><br />BounBang幫棒已收到您提供的個人資料，並依據平台使用條款審核中，審核結果將用e-mail方式通知您。<br />若您有個人資料需要修改，請至基本設定>個人資訊/身分認證 進行修改<br /><br /><array><br /><br /><btn1><br /><br />'),
		  '3-022'=>array('title'=>'BounBang幫棒好幫手 – 好幫手個人資料審核成功通知','body'=>'<name> 您好，<br /><br />您的好幫手個人資料審核成功！歡迎您成為BounBang幫棒 好幫手。<br /><br />您可以在服務設定內設定您提供的服務並刊登，就開始您的生意了!<br /><br /><array><br /><br />'),
		  '3-023'=>array('title'=>'BounBang幫棒好幫手 – 好幫手個人資料審核失敗通知','body'=>'<name> 您好，<br /><br />您的好幫手個人資料審核失敗！由於您提供的資料不完整或者上傳的證件照片不清晰，導致審核失敗。<br /><br />您可以在基本設定>個人資訊/身分認證 進行修改後並送出，我們再收到您更新的資料後會進行審核，並將審核結果用e-mail方式通知您<br /><br /><array><br /><br /><btn1><br /><br />'),
		  '3-024'=>array('title'=>'BounBang幫棒好幫手 – 好幫手服務資料已公開','body'=>'<name> 您好，<br /><br />您的好幫手服務資料已公開，客戶將依據您公開的服務資料詢問您或雇用您。<br /><br />您可以至我的工作>配對/預約 查詢客戶對您提出的詢問或者是雇用需求<br /><br />'),
		  '3-025'=>array('title'=>'BounBang幫棒好幫手 – 好幫手服務資料已關閉','body'=>'<name> 您好，<br /><br />您的好幫手服務資料已關閉，客戶將無法看到您提供的服務資料也無法雇用您。<br />若您要繼續讓客戶看到您的服務資料或雇用您，您可以至服務設定 下開啟。<br /><br />'),
		  '3-026'=>array('title'=>'<Name> 推薦您BounBang幫棒的服務鈴(配對)工作','body'=>'<name> 您好，<br /><br /><Name> 推薦您BounBang幫棒的服務鈴(配對)工作。<br /><br /><array><br /><br />您可至 '.url('/').' 做詳細工作查詢與接案。<br /><br />如果您還沒有加入BounBang幫棒，您可以透過下列的邀請連結加入BounBang幫棒。<url><br /><br />'),
		  '3-026-1'=>array('title'=>'推薦服務鈴工作給朋友 – FB/Line','body'=>'<name> 您好，<br /><br /><name1> 推薦您BounBang幫棒的服務鈴(配對)工作。<br /><br /><array><br />您可至 '.url('/').' 做詳細工作查詢與接案。<br /><br />如果您還沒有加入BounBang幫棒，您可以透過下列的邀請連結, 加入BounBang幫棒。<br /><url><br /><br />'),
		  
	  );
	  return $data[$kind];
  }
	
}
