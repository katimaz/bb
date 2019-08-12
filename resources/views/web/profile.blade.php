@extends('web.base')
@section('content')
<script src="https://maps.google.com/maps/api/js?key={{ config('services.google_api.key') }}&libraries=places"></script>
<div class="item-title">個人資訊</div>
<div id="app" class="container">
    <div class=" offset-md-2 col-md-8"> 
    <div class="alert alert-warning alert-dismissible fade show" v-if="user.usr_status<1" role="alert">
      <span v-if="!user.email_validated"><strong>恭喜您註冊成功!</strong> 接下來您需要填完各項基本設定，並完成郵件驗證程序才能完整使用本站功能。</span>
      <span v-else="!user.email_validated"><strong>恭喜您信箱驗證成功!</strong> 接下來請填完各項基本設定，並按下一步就能完整使用本站功能。</span>
      <button type="button" class="close" data-dismiss="alert" aria-label="Close">
      <span aria-hidden="true">&times;</span>
      </button>
    </div>
    <div class="pro-box">
      <form id="mainFrm" action="<?=URL::to('/')?>/web/profile_pt" method="post" enctype="multipart/form-data">
      @csrf
      	<input type="hidden" name="old_email" v-model="old_email" />
        <input type="hidden" name="count" id="count" v-model="positions.length" />
        <div class="containers">
            <div class="imageWrapper">
                <img class="image" src="{{(($user->usr_photo) ? URL::to('/') . '/avatar/small/' . $user->usr_photo : 'asset("/images/person-icon.jpg")')}}">
                <div class="file-upload">
                    <input type="file" name="avatar" id="avatar" class="file-input">
                    <i class="fa fa-camera" aria-hidden="true"></i>
                </div>
            </div>   
        </div>
        <div class="form-group row">
            <label  class="col-sm-2 col-form-label">登入身份<i class="text-danger">*</i></label>
            <div class="col-sm-10">
                <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="usr_type" value="0" v-model="user.usr_type" id="c" >
                <label class="form-check-label" for="c">客戶</label>
                </div>
                <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="usr_type" value="1" v-model="user.usr_type" id="h">
                <label class="form-check-label" for="h">好幫手</label>
                </div>
            </div>
            <div class=" offset-sm-2 col-sm-10" v-if="user.usr_status<1">
              <input class="form-check-input fix" v-model="agree" type="checkbox" id="ig" required>
              <label class="form-check-label" for="ig">我已閱讀完畢並同意</label><a href="javascript:void(0)" class="payway" data-toggle="modal" data-target=".bd-example-modal-lg">(使用者條款)</a>．<i class="text-danger">*</i>
            </div>
        </div>
        <div class="form-group row">
        <label for="inputPassword3" class="col-sm-2 col-form-label">真實姓名 <b class="text-danger">*</b></label>
        <div class="form-row col-sm-10">
        <div class="col">
        <input type="text" class="form-control" placeholder="姓" name="last_name" id="last_name" v-model="user.last_name">
        </div>
        <div class="col">
        <input type="text" class="form-control add5" placeholder="名" name="first_name" id="first_name" v-model="user.first_name">
        </div>
        </div>
        </div>
        <div class="form-group row">
        <label  class="col-sm-2 col-form-label">手機號碼 <b class="text-danger">*</b></label>
        <div class="form-row col-sm-10">
        <div class="col-4">
        <input type="text" class="form-control" name="phone_nat_code" v-model="user.phone_nat_code">
        </div>
        <div class="col-8">
        <input type="text" class="form-control add5" name="phone_number" id="phone_number" v-model="user.phone_number" placeholder="例:901234567">
        </div>
        </div>

        </div>
        <div class="form-group row">
          <label  class="col-sm-2 col-form-label">Email <b class="text-danger">*</b></label>
          <div class="col-sm-8">
          <input type="email" class="form-control" name="email" id="email" v-model="user.email" @change="is_existed" required placeholder="請填寫郵件信箱">
          </div>
          <div class="col-sm-2">
          	<a href="javascript:void(0)" v-if="!user.usr_status && !is_tomail && !user.email_validated" @click="veri_mail" class="text-danger" ><i :class="((sending)?'fa fa-spinner fa-pulse':'fa fa-paper-plane')" aria-hidden="true"></i> 送出驗證</a>
            <a href="javascript:void(0)" v-if="is_tomail" class="text-primary"><i class="fa fa-paper-plane" aria-hidden="true"></i> 已送出驗證Email。請至Email 信箱完成驗證。</a>
            <a href="javascript:void(0)" v-if="user.email_validated && !existed" class="text-success"><i class="fa fa-paper-plane" aria-hidden="true"></i> 已驗證</a>
            <a href="javascript:void(0)" v-if="existed" class="text-danger">郵件重複</a>
          </div> 
        </div>
        <div class="form-group row">
        <label  class="col-sm-2 col-form-label">性別 <b class="text-danger">*</b></label>
        <div class="col-sm-10">
        <div class="form-check form-check-inline">
        <input class="form-check-input" type="radio" name="sex" id="w" value="2" v-model="user.sex">
        <label class="form-check-label" for="w">女</label>
        </div>
        <div class="form-check form-check-inline">
        <input class="form-check-input" type="radio" name="sex" id="m" value="1" v-model="user.sex">
        <label class="form-check-label" for="m">男</label>
        </div>
        <div class="form-check form-check-inline">
        <input class="form-check-input" type="radio" name="sex" id="x" value="0" v-model="user.sex">
        <label class="form-check-label" for="x">不揭露</label>
        </div>
        </div>
        </div>
        <div class="form-group row">
        <label  class="col-sm-2 col-form-label">更新密碼</label>
        <div class="col-sm-10">
        <input type="password" class="form-control" id="password" name="password" v-model="user.password"  maxlength="10" placeholder="不更新密碼請勿輸入">
        </div>
        </div>
        <div class="form-group row">
        <label  class="col-sm-2 col-form-label">確認密碼  </label>
        <div class="col-sm-10">
        <input type="password" class="form-control" id="chk_password" name="chk_password" v-model="chk_password"  maxlength="10" placeholder="不更新密碼請勿輸入">
        </div>
        </div>
        <div class="form-group row"> 
    		<div id="lable_area" class="col-sm-2"> 
            	<label class="col-form-label">常用地址 <b class="text-danger">*</b>
                	<br class="d-none d-sm-block"> 
            		<a href="javascript:void(0)" @click="add_address" class="text-danger addadd">增加地址<i class="fa fa-plus-circle" aria-hidden="true"></i></a>
               	</label>
                <div id="menuBtn" class="w-100" style="margin-top:48px; display:none;"><a href="javascript:void(0)" onclick="lessBtn();" class="text-dark">減少地址<i class="fa fa-minus-circle" aria-hidden="true"></i></a></div>
            </div>
            <div class="col-sm-10">
                <div class="add-box mb-2" v-for="(position,num) in positions">
                    <div class="b-close" v-if="num" @click="lessBtn(num)"><i class="fa fa-times-circle" aria-hidden="true"></i></div>
                    <div id="twzipcode d-table">
                    	<select class="county float-left" @change="select_city(num)" :name="'city'+num" :id="'city'+num" v-model="position.city">
                            <option value="" v-text="'縣市'"></option>
                            <option v-for="(city,index) in citys" :value="city" v-text="city"></option>
                        </select>
                        <select class="district county float-left" @change="select_nat(num)" :name="'nat'+num" :id="'nat'+num" v-model="position.nat">
                            <option value="" v-text="'鄉鎮市區'"></option>
                            <option v-for="(area,key,index) in position.areas" @change="position.zip=key"  :value="key" v-text="key"></option>
                        </select>
                        <input class="zipcode county float-left" :name="'zip'+num" :id="'zip'+num" v-model="position.zip" placeholder="郵遞區號" maxlength="3" readonly="readonly" />
                    </div>
                    <input type="text" class="form-control" :name="'addr'+num" :id="'addr'+num" v-model="position.addr"  @blur="get_latlng(num)" @change="get_latlng(num)">
                    <input type="hidden" :name="'lat'+num" :id="'lat'+num" v-model="position.lat" />
                    <input type="hidden" :name="'lng'+num" :id="'lng'+num" v-model="position.lng" />
                </div>
            </div>
    	</div>
        <div class="form-group row">
            <div class="offset-sm-2 col-sm-10">
                <a class="btn btn-lg btn-success" href="javascript:void(0)" @click="sendform"><i class="fa fa-spinner fa-pulse" v-if="next_sending" aria-hidden="true"></i> 下一步</a> 
            </div>
        </div>
      </form>     
    </div>
  </div>    
</div>
<div class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLongTitle">BounBang幫棒使用者條款
</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
            <h5>使用者服務條款</h5>
          <hr>
        <p><strong>條款之接受及規範</strong> <br>
          各位使用者您好，歡迎您註冊加入BounBang幫棒平台（以下稱「本平台」），以下是本平台的使用者服務條款，其中之記載都將構成您與本平台間之法律合約關係，並規範您與本平台間之權利義務關係，請務必詳細閱讀，以瞭解我們之服務內容及您的權利義務。如您註冊帳號並開始使用本平台之服務，即代表您同意以下條款。您瞭解並同意，您在使用本平台時必須遵守本平台之所有相關條款、管理規範、內部規則、聲明、政策、公告與協議，以及所有未來的修正規範，以上所有規範皆構成您與本平台間之契約，您願意受其拘束，具有法律效力。如您不同意以上各項規範，請您勿使用本平台所提供之服務。此外，本平台亦保留拒絕使用者使用本平台之權利。 </p>
        <p><strong>條款之修正</strong> <br>
          本平台無論在何時且無庸事先通知的情況下，均有權修改本平台之所有相關條款、管理規範、內部規則、聲明、政策、公告與協議。各種規範如有修正之情形，本平台將透過本平台網站發佈修正通知，或者在使用者登入帳戶時，收到本平台之訊息通知。修改之內容將在修改公告在本平台或發送修改通知給使用者七天後生效，以較早者為準。如使用者在各項規範修改發布後或接收該通知七天後仍繼續使用本平台，即視為同意各項規範之修改。使用者如不同意各項規範之修改，請勿繼續使用本平台所提供之服務。 </p>
        <p><strong>第一條：名詞定義</strong></p>
        <ol>
          <li>本平台：指本平台之包括網站、行動裝置軟體、行動裝置ÅPP，或其他應用程式、軟體之形式，以供本平台提供服務或使用者使用服務。 </li>
          <li>使用者：指完成本平台帳戶註冊或通過本平台提交或接收服務請求的人員，包括但不限於服務提供者和消費者等客戶成員。 </li>
          <li>消費者：指註冊為本平台帳戶之使用者之中，接受或請求服務項目報價，或以其他方式接受、評估、委託、僱用或支付服務提供者提供之服務項目者，亦稱客戶。 </li>
          <li>服務提供者：指註冊為本平台帳戶之使用者之中，針對服務項目發送報價單，或透過本平台提供服務項目或收取服務項目費用者，亦稱好幫手。 </li>
          <li>服務項目：指由服務提供者所表列、報價、預定或提供之服務<a name="_Hlk2417733">或商品</a>，或者是由消費者通過本平台所提出預約、請求或接受之服務或商品，亦稱案件。 </li>
          <li>交易金額：指在本平台上由消費者向服務提供者所下訂單之消費金額。 </li>
          <li>平台服務費：指本平台向服務提供者收取之服務費，計算方式為交易金額之20%。 </li>
          <li>服務費盈餘: 指本平台收取的服務費扣除營運成本及支出後，所剩餘的結餘。 </li>
          <li>平台內容：指由本平台製作、提供或上傳之所有內容，包括但不限於文字、言論、圖形、圖像、影片，音樂、軟體、音訊、影像檔、資訊或其他素材。 </li>
          <li>使用者內容：指由使用者製作、提供、上傳，或透過本平台提交、上傳、發佈或傳輸之內容，包括但不限於服務提供者資訊、服務項目需求、服務說明、報價單、往返訊息、評價、預約時程和日曆資訊、評價、照片、檔案、資訊、文字、言論、圖形、圖像、影片，音樂、軟體、音訊、影像檔、資訊或其他素材。 </li>
          <li>損害賠償：本條款所稱之「損害」或「損害賠償」，均包含直接損害、間接損害、經濟損失、營業損失、違約金、罰鍰、罰金、和解金、成本及費用（包括但不限於律師費、其他糾紛調解費用）。 </li>
        </ol>
        <p><strong>第二條：使用者管理</strong></p>
        <ol>
          <li>使用者如欲使用本平台所提供之服務配對服務，需先行於本平台申請註冊<a name="_Hlk534724866">（包含本站註冊及經由其他網站帳號登入註冊）</a><a name="_Hlk534724937">，</a>並同意本平台規定之使用條款及規則，且需於註冊時提供本平台要求之使用者資料，以供本平台建立相關資料庫，供服務之配對。 </li>
          <li>本平台之帳號為免費申請，得使用本平台所提供之特定限定範圍之免費或付費服務（依平台內設定為準，本平台有隨時調整之權利）。 </li>
          <li>使用者必須使用自己的真實個人資訊註冊，不得使用他人之個人資料註冊，亦不得將帳號轉讓、贈與、買賣給其他人或與他人共用。 </li>
          <li>使用者帳號之設定內容、名稱、圖片、說明，不得使用模仿、抄襲、攻擊性、歧視性、騷擾性、不雅、猥褻、詐騙或煽惑犯罪、構成犯罪、廣告、具商業目的之素材，或其他侵害他人權益、洩漏他人個人資料之名稱，違反上述規定者，本平台有權凍結帳號、終止服務或採取一切管理措施。 </li>
          <li>使用者同意在註冊本平台帳號時或提出服務需求過程中或在所有使用本平台之其他時間，皆提供準確、最新和完整的資訊，並於使用本平台過程中即時更新資訊以保持其準確性、最新性和完整性。使用者將完全負責維護自己的本平台密碼。使用者完全性自行負責本平台帳戶上所發生之任何行為，如有任何未經授權的使用情形出現時，使用者同意於發現後立即通知本平台。本平台對任何一方因未經授權而使用使用者之帳戶所造成的任何損失概不負責。如有任何上述非法使用情形出現，使用者同意承擔此種未經授權的使用致生本平台或其他人損失之損害賠償責任。 </li>
        </ol>
        <p><strong>第三條：使用資格</strong> <br>
        本平台僅同意年滿20歲之人使用本平台之服務，否則應取得法定代理人之同意，使用者於本平台開設帳號或使用本平台所提供服務時，視同對本平台主張及宣稱為具有完全之行為能力者或已取得法定代理人之同意，並同意依民法規定，負擔相關民事責任。 </p>
        <p><strong>第四條：中介服務</strong></p>
        <ol>
          <li>本平台為專業網路中介平台服務者，提供使用者透過網路或行動設備使用本平台之服務，服務提供者可透過本平台提供消費者服務項目，消費者可透過本平台提出服務項目之需求或指定與某位服務提供者聯繫。本平台僅提供消費者的服務需求訊息予符合需求之服務提供者，如服務提供者認為可提供消費者所需之服務，服務提供者可透過本平台提供之資料向消費者進行服務項目報價、提案及協商。倘消費者同意選擇與服務提供者合作，服務提供者將自行與消費者聯繫提供相關之服務，消費者將另行直接與服務提供者間成立其他民法規定之契約關係，並同意經由本平台之代收付款方式支付服務提供者所提供的服務項目費用，倘日後生相關爭議，消費者及服務提供者皆同意該民法契約行為爭議與本平台無涉，應自行尋求爭議解決之途徑。 </li>
          <li>使用者皆明瞭並同意本平台僅提供網路中介平台服務，服務提供者與消費者就彼此間之服務內容如有發生爭議，皆屬消費者與服務提供者間之爭議，該爭議皆與本平台無涉，本平台無庸負擔任何相關履行、監督、擔保或賠償責任，亦無負責協調消費爭議之責任。服務提供者知悉並同意，在本平台上註冊帳號後，服務提供者只能獲得透過使用本平台而對於接受服務項目感興趣之人之洽詢，包括但不限於提供消費者資訊，發送簡訊或安排工作預約等行為，進而達成提供專業服務，惟本平台並不保證本平台使用者將一定會使用他們的服務項目，本平台亦不擔保服務提供者或消費者可於一定時間內配對成功且由服務提供者提供消費者完全滿意之服務或消費者自服務提供者處取得完全滿意之服務。 </li>
          <li>服務提供者僅透過本平台提供服務給消費者，服務提供者並非屬本平台之員工、合資對象、合作夥伴或代理商。服務提供者知悉其應設定或確認自己的價格，使用自有之設備，並確定自己的工作時間表，工作相關內容皆與本平台無涉，本平台對於服務提供者所提供的服務（包括服務提供者提供此類服務的方式）並不加以擔保及控制品質。 </li>
          <li>藉由註冊或使用本平台提供、張貼或提供服務項目，服務提供者表示並保證他們以及為他們執行工作的受雇者、代理人、承包商和分包商，都是適合且完全符合資格、有經驗、具備執照、經認證的、具擔保、且已投保。根據適用法律或法規的要求，服務提供者得在法令管轄範圍內提供專業服務，且該服務與其正在執行的具體工作相關。 </li>
          <li>如本平台發現服務提供者具有相關刑事定罪之紀錄，本平台得依據本平台之衡量，限制、暫停、停用或取消服務提供者之帳戶，服務提供者同意提供此類資訊予本平台。 </li>
        </ol>
        <p><strong>第五條：權利聲明</strong> <br>
        本平台聲明並保留本平台內容之一切相關權利、技術、資訊，包括但不限於：所有權、專有權、智慧財產權等。本平台的使用者僅得在線上刊登、閱覽、討論、媒合中介及履行服務內容之目的與範圍內，得正當且合法的使用本平台之服務及一切相關權利、技術、資訊，不得為任何非授權範圍及非正當目的之使用。 </p>
        <p><strong>第六條：</strong><strong>第三方連結、委外金流及外部廣告：</strong><strong> </strong></p>
        <ol>
          <li>使用者瞭解並同意，本平台中可能出現：第三方連結、第三方營運之產品服務、委外金流及置入廣告之情形（以下總稱第三方服務），此類服務內容均非由本平台所提供。使用者於點選或存取第三方服務時，將可從第三方之標示清楚辨識該服務係由第三方所提供，而非本平台所提供。 </li>
          <li>本平台有可能與合作廠商置入廣告內容，包括但不限於文字、圖片、樣品等，該廣告內容皆由廣告商、產品或服務的提供廠商所提供，對於廣告內容使用者應自行斟酌判斷其適合性及真實性，本平台對於廣告內容不負任何擔保責任。 </li>
          <li>任何第三方服務廠商透過本平台或其他服務與網站提供給使用者的任何意見、陳述、價格、要約與其他內容或資訊，均歸屬於該第三方之責任。對於該資訊的完整性、正確性、可用性或可靠性，本平台不為該第三方保證或擔負任何法律上的責任。使用者應該自行判斷並選擇合適的內容瀏覽或下載，以免造成使用者個人的損害。 </li>
          <li>所有第三方服務均係由第三方所提供，非屬本平台所經營或可得控制之範圍。使用者與第三方間之服務，係屬使用者與第三方間之契約關係，與本平台無涉，除非經本平台明示保證者外，如有任何爭議、疑義或損失，均應由使用者與第三方自行協調，本平台不負擔保、協調、代追償、協助訴訟、代履行、退費或賠償責任。 </li>
          <li>本平台對於金流服務廠商之服務效能及服務品質，不負任何責任及保證。如因金流服務廠商端或使用者端所生之事由（包含但不限於：系統異常、盜用冒用、人為操作不當等），造成付款問題時，本平台亦不負責處理退款事宜 </li>
        </ol>
        <p><strong>第七條：代收付款及金流手續費</strong> <br>
        本平台使用智付寶金流服務，金流服務之交易手續費用將由服務提供者負擔，此金流服務以代收代付的第三方支付方式向消費者代收費用，並再扣除本平台服務費及交易手續費後將剩餘金額轉支付給服務提供者，服務提供者所提供之商品及服務須符合金流公司及銀行規定許可之項目，本平台不介入許可項目審核，代收代付詳細說明如下:</p>
        <ol>
          <li>消費者：消費者使用本平台提出使用服務需求，並同意選擇與服務提供者合作時<a name="_Hlk534999328">，</a>本平台會向消費者代收支付給服務提供者的全額費用。 </li>
          <li>服務提供者：服務提供者使用本平台並與消費者合意提供服務時，於服務提供者完成服務並得到消費者與服務提供者雙方確認後，本平台會將向消費者代收轉支付給服務提供者的金額中扣除本平台服務費及智付寶金流服務交易手續費後，剩餘金額轉支付給服務提供者 </li>
          <li>轉支付款項時程: 在確認消費者及服務提供者工作完成後<a name="_Hlk535000678">，</a>本平台會進行轉支付款項流程，為了配合帳務結算週期，各款項需俟收單銀行及信託銀行核發撥付後，本平台始能進行相關帳務作業，並於扣除平台服務費及交易手續費後轉支付給服務提供者。如遇天災、不可抗力等事故，將可能延後相關撥款進度，而本平台就此所生之延後付款，對於服務提供者不負遲延責任 </li>
        </ol>
        <p><strong>第八條：電子發票</strong> <br>
        本平台依法將於符合當地法令規定之狀態開立電子發票予收取平台服務費的對象付款人，本平台使用PAY2GO&nbsp;開立發電子發票，發票採每個月開一次方式進行，在每個月月底會將該月的消費開立在同一張發票內，購買後被收取平台服務費使用者將收到來自PAY2GO發送的電子發票通知，&nbsp;本平台將不再寄發紙本發票。使用者同意於核發電子發票之目的及範圍內，本平台得提供相關之資訊給PAY2GO公司，使用者同意遵守PAY2GO相關條款。 </p>
        <p><strong>第九條：回饋金</strong></p>
        <ol>
          <li>使用者依本平台發布之相關回饋金規則可以獲得回饋金，使用者所獲得之個人回饋金不得轉讓、轉售，本平台會在每年年度結束時寄發個人扣繳憑單做為使用者個人年度回饋金所得的憑據。 </li>
          <li>回饋金制度依照本平台相關規定進行，如有未盡事宜，本平台保留最終解釋權，並得隨時修改調整之。 </li>
        </ol>
        <p><strong>第十條：擔保及聲明</strong></p>
        <ol>
          <li>使用者承諾提供給本平台之資訊皆為正確無誤，包括但不限於名字、電話、地址或電子郵件等資訊，如使用者提供錯誤之訊息，導致本平台遭致相關之直接性或間接性損害，使用者同意負擔此損害以及所衍生之相關費用。 </li>
          <li>本平台之使用者皆同意自行承擔風險，本平台並不保證或擔保使用者、服務提供者或消費者所提供之資料正確性，如有發生任何資料不正確之情事，本平台皆不負擔任何損害賠償責任。 </li>
          <li>使用者皆擔保其提供本平台之所有資料均屬真實，無任何詐欺、虛偽、引人錯誤、誇大不實或有違反法令及公序良俗之情事；如有虛偽陳述之情事，致使用者或本平台任一方遭受損害，虛偽陳述者同意自行負擔相關損害賠償責任，本平台則免除任何損害賠償責任。 </li>
          <li>使用者應就其於本平台上的使用者內容負責。使用者保證：（1）使用者是所有使用者內容的唯一和專屬所有權人，使用者有權提供本平台使用，或者使用者擁有授予本平台所需的所有權、許可、同意和發佈等權限；（2）使用者內容或使用者透過本平台將使用者內容（或其任何部分）的使用、上傳、發佈、提交或傳送，都不得侵犯、挪用或任何人的專利、著作權、商標、營業秘密、know-how或其他專有權或智慧財產權，或公開或隱私權的權利。或導致違反任何適用的法律或法規；（3）本平台就使用者內容不負任何審查、核對、監督之責，使用者應確保其使用者內容之正確性，並符合使用者條款及規則所約定之陳述和保證。 </li>
          <li>服務提供者提供之廣告、服務資訊、 報價或其他內容於本平台，如有任何違反中華民國法令之疑慮，經檢舉或通知者，本平台無庸經服務提供者同意，得直接下架該內容，服務提供者不得有任何異議。 </li>
          <li>使用者聲明且保證，使用者製作、提供、上傳，或透過本平台提交、上傳、發佈或傳輸之使用者內容，及使用者發表之言論、意見、評論等，不論為公開或非公開，均應自負言論及法律責任。使用者不得透過本平台刊登、發表或傳送給公眾或其他使用者任何不適當的內容，包括但不限於：誹謗、辱罵、猥褻、歧視、冒犯、性目的、脅迫、騷擾、詐騙、非法資訊、明知為錯誤、意圖誤導、虛假和不正確的資訊、政治性、與服務無合理關聯性、違反或侵犯第三方權益之內容。 </li>
          <li>使用者同意並擔保本平台及其股東、子公司、關係企業、董事、經理人、代理人、合作夥伴及員工，使上述人等免於承擔因下列事由所導致或相關之任何主張、請求、程序及訴訟，如有上述情形，使用者應負擔全部責任並負責出面處理，因此所生之所有損害，均由使用者負擔，並應向上述人等賠償：（1）使用者違反或觸犯本服務條款的任何條款，或本服務條款所提及的任何政策或準則；（2）使用者使用或濫用本服務的行為;（3）使用者違反法律或侵害任何第三人權利。使用者並同意基於合法目的使用本服務，並會遵守本服務條款及所有適用法律、規定、守則、指令、準則、政策及規範。 </li>
          <li>服務提供者應擔保其提供之服務及提供服務人員之資格皆符合中華民國政府法令一切規定，服務提供者應具備依法所需之任何政府核准證明或其他相關文件，如有任何違反中華民國法令規定之情形，致本平台遭行政機關課處罰鍰或其他法律處分，服務提供者同意無條件負責所有衍生之法律責任並負擔相關損害賠償責任。 </li>
          <li>專業人士之免責聲明：本平台已採取相對應之措施盡力驗證提供專業服務人士之證照，然如其提供不實之資訊，導致本平台隨之提供該訊息給消費者，本平台無法承諾並確認該訊息之正確性及承擔相關之責任，故消費者就其提出相關專業服務之需求時，消費者同意自行於接受服務提供者之報價時，並於確認接受服務前向服務提供者請求提出相關證照。如消費者未自行向服務提供者確認相關證照致生損害時，消費者同意本平台無庸負擔任何責任。 </li>
          <li>使用者承諾並擔保，就服務內容或相關事項之詢問、洽談、商議或合意，均應依本平台設定之程序、方式及對話管道進行，不得意圖為規避本平台之中介程序及平台服務費，而以任何方式規避、不使用、使用非本平台之機制而私下為服務之洽詢及合意，如有上開情形，應賠償該筆違規交易服務費總金額十倍之違約金予本平台。 </li>
        </ol>
        <p><strong>第十一條：禁止事項</strong> <br>
        使用者不得從事以下之行為： </p>
        <ol>
          <li>使用他人的帳戶，錯誤陳述自己或通過本平台提供的服務項目，虛偽顯示身份或資格，在提供需求報價過程中錯誤陳述項目或其他資訊，或將服務項目張貼在本平台之不適當的分類項目中。 </li>
          <li>借由本平台之任何程序、流程、步驟，對其他使用者為騷擾、傷害、追蹤、犯罪、詐騙、侵犯等一切不法行為，或對其他使用者發送垃圾電郵、直銷、連鎖信或其他商業廣告信，或其他一切推銷商品或服務之行為。 </li>
          <li>對本平台內容或自本平台取得之資訊進行修改、更改格式、複製、重製、顯示、發布、重新張貼、傳送、公布、授權、銷售、出租、轉移或改作。 </li>
          <li>聲稱或使用電腦程式或其他設備方法偽冒與本平台、品牌、網站或服務相關聯，進而誤導他人到訪其他網站。 </li>
          <li>使用任何人工或自動方式或應用其他自動搜尋軟體或設備抓取、蒐集、索引、探勘本平台的內容與使用者資料，或以任何方式重製或規避本平台的正常瀏覽架構及呈現模式。 </li>
          <li>刊登、傳送或散布病毒、木馬或類似惡意的電腦與行動裝置程式語言、檔案或程序，或修改、改寫、翻譯、銷售或用反向工程方式還原或破解本平台的內容、技術或軟體。 </li>
          <li>以任何手動或自動方式複製受版權保護的內容，或以其他方式誤用或盜用本平台之資訊或內容，包括但不限於同質性、競爭性或協力廠商網站上使用。 </li>
          <li>在一定之時間內，於本平台提出過多不合理之需求或該需求明顯非一般瀏覽器或人工所提出。 </li>
          <li>採取任何可能不合理地毀壞本平台基礎設施的行為，包括但不限於：（1）干擾或企圖干擾本平台或第三方協力廠商的正常營運；（2）企圖閃避或繞過用於防止或限制進入本平台的措施；（3）繞過、使其無效或以其他方式干擾本平台的安全功能；（4）散佈可能損害本平台之病毒或任何其他技術；（5）使用本平台致侵害任何或第三方協力廠商的版權、營業秘密或其他權利，包括隱私權或宣傳權利。 </li>
          <li>任何對本平台之運作造成干擾或異常，致影響本平台正常運作，或干擾其他使用者正常使用本平台之行為。 </li>
          <li>從本平台收集其他使用者之個人資料，包括但不限於名稱或其他帳戶資訊，或使用本平台提供的通信系統，卻非基於條款約定之用途使用，包括用於商業招募、徵才、廣告之目的。 </li>
          <li>針對服務提供者或消費者提出招聘、徵才行為，以進行就業或求才為目的或任何不符合本平台目的的使用。 </li>
          <li>採取任何不適當或非法的行動，包括通過本平台提交不適當或非法的內容，包括騷擾、可恨、非法、褻瀆、淫穢、誹謗、威脅或歧視的內容，或提倡、促進或鼓勵不適當的活動，此可能被視為刑事犯罪的行為，或引起民事責任或違反任何法律的行為。 </li>
          <li>違反任何本平台有關本平台之使用以及本平台與使用者間規則。 </li>
          <li>廣告或徵求與本平台未設置或不符目的的服務項目，包括但不限於：（1）不屬於服務項目支援的類別或僅提供產品販賣的任何服務項目；（2）提供目錄或轉介；（3）提供貸款；（4）提供租用空間；（5）從事與本平台的業務競爭；（6）推廣或提供龐氏騙局、垃圾郵件或未經消費者主動邀求的商業內容等；（7）提供非合法之服務項目。 </li>
          <li>從事任何可能損害審查或評分、評價、制度有效性或準確性之行為。 </li>
          <li>服務提供者未按承諾執行服務項目，除非消費者未能實質執行雙方已同意的服務協定與條款或拒絕付款或出現明顯的錯誤，或者服務提供者無法驗證客戶的身份。 </li>
          <li>從事欺詐行為。 </li>
          <li>進行任何活動或從事與本平台的業務或目的不符的行為。 </li>
          <li>無真實需求或無付款意願下提出預購、請求、洽談或服務項目之需求。 </li>
          <li>服務提供者所提供之商品或服務不得有下列情形： </li>
          <li>涉及色情或賭博性交易之商品。 </li>
          <li>未取得行政院公平交易委員會核准且非傳銷公會使用者之直銷/多層次傳銷公司商品。 </li>
          <li>違反商標權、專利權、著作權或其他侵害他人權利之商品。 </li>
          <li>非實際消費性之簽帳融資墊付現款（俗稱調現）或其他變相之融資。 </li>
          <li>處方藥物或煙草製品。 </li>
          <li>任何與暴力相關之商品或勞務。 </li>
          <li>違反政府法令或相關金融法規或社會善良風俗之商品。 </li>
        </ol>
        <p><strong>第十二條：調查及管制措施</strong> <br>
        本平台為提供並維護良好之營運環境，並遵循國家法令，使用者如有違反本條款或相關規範與協議之任何違紀情形，經他人檢舉或通知者，本平台均有權隨時介入調查，並依違紀情節，自行決定採取下述相關管制措施： </p>
        <ol>
          <li>通知使用者要求限期改善。 </li>
          <li>暫時或永久停止部分服務內容。 </li>
          <li>暫時停權凍結帳號3至7日。 </li>
          <li>永久停權凍結帳號。 </li>
          <li>修改移除相關內容。 </li>
          <li>移送行政司法機關處調查處理。 </li>
          <li>其他必要措施。 </li>
        </ol>
        <p><strong>第十三條：保密義務</strong></p>
        <ol>
          <li>使用者所持有本平台提供之資訊，包括但不限於使用者之個人資料或其他未經公開揭露之資訊，應盡善良管理人之注意義務進行保密，除依本合約規定利用外，非經本平台事前書面同意，不得自行利用或洩漏前述未公開資訊予任何第三人。如使用者有對其員工、顧問或其認為有必要之第三人揭露者，應使該等受揭露之對象受與本合約相當之保密義務。 </li>
          <li>本條約定不因本合約之解除、終止或屆滿而失其效力，至機密資訊非因可歸責於使用者之事由喪失其秘密性為止。本平台得隨時以書面通知使用者刪除或返還該機密資訊。 </li>
        </ol>
        <p><strong>第十四條：個人資料保護</strong></p>
        <ol>
          <li>本平台尊重並且致力保護使用者之隱私，本平台處理使用者個人資料的政策與原則，以及蒐集利用之範圍及目的，均於本平台之「隱私權政策」中詳細規範。本平台之「隱私權政策」構成本服務條款之一部分，與本服務條款具有同等之法律效力。 </li>
          <li>服務提供者知悉並了解本平台所提供之使用者個人資料，皆係本平台依「個人資料保護法」蒐集、處理及利用，服務提供者僅得依本平台提供之資料於提供服務之必要範圍內進行個人資料之利用，並不得對使用者為任何行銷或其他個人資料利用之行為。 </li>
          <li>服務提供者需遵守「個人資料保護法」及相關法令之規定，對於本平台所提供之使用者個人資料，皆應採取適當之安全措施，避免造成消費者之任何損害。 </li>
          <li>若服務提供者違反「個人資料保護法」及相關法令規定或違反本合約任何規定，致生個人資料爭議者，服務提供者應即時通報本平台，並依本平台指示進行處理，服務提供者同意自行負擔相關法律責任，如導致本平台因而受有損害，並應賠償本平台相關損害。 </li>
        </ol>
        <p><strong>第十五條：智慧財產權</strong></p>
        <ol>
          <li>本平台中所有可得觀看之文字、圖形、編輯內容、圖表、設計、照片、影像、字體以及其他內容，皆屬本平台或合法權利人所有，並受到中華民國相關法令之保障，使用者不得未經本平台或合法權利人之事前書面同意，即任意加以重製或從事任何違反著作權法或商標法之行為。 </li>
          <li>本平台尊重並且致力保護智慧財產權，本平台保護智慧財產權的政策與原則，以及爭議之處理，均於本平台之「智慧財產權政策」中詳細規範。本平台之「智慧財產權政策」構成本服務條款之一部分，與本服務條款具有同等之法律效力。 </li>
        </ol>
        <p><strong>第十六條：免責事項</strong></p>
        <ol>
          <li>使用者瞭解並同意應自行承擔使用本平台專業服務之相關風險。本平台僅依據使用者提供之資訊提供資訊中介之服務，本平台對於服務提供者之服務內容不作任何明示、暗示或法定的擔保、主張或聲明，包括但不限於對於品質、效能、真實性準確性、完整性、無侵權或特定用途適用性之擔保，或於交易過程中按行業常規而衍生之擔保。本平台不保證本平台或其中所含功能，或該服務、網站、功能可隨時被確認、不會中斷、及時提供、安全可靠、正確、完整或無錯誤。本平台亦不對任何第三人之毀謗或非法行為負責，或因透過本平台所使用任何資料、資訊、素材、所遭致之任何損害而負責。此外，本平台不擔保網站平台之任何資訊之正確性。 </li>
          <li>使用者完全自行承擔與本平台的其他使用者的所有通信和交互往來之相關責任，包括但不限於任何服務提供者、消費者、服務提供者之相關成員。使用者了解本平台不保證會驗證、審核或檢查本平台使用者的任何聲明與內容。本平台針對使用者的行為或與本平台的任何當前或未來使用者的相容性不作任何陳述或保證。使用者同意在與本平台的其他使用者間有關所有通信和交互往來中，所進行之任何交流自行採取合理的預防措施，特別是使用者決定離線或是親自會面以提供或接受專業服務。本平台對任何使用者或第三方協力廠商的任何行為或有所遺漏時所產生的所有責任皆不負責。 </li>
          <li>本平台對於因使用或無法使用本平台或服務，所產生之全部風險或所造成之任何損害，皆不負擔損害賠償責任（無論是基於合約責任、侵權行為責任、產品責任或其他任何法律相關責任）。本平台就以下情形，均不負擔損害賠償責任：（1）任何附帶的、特殊的相應的損害，包括利潤損失、資料滅失或商譽損失；（2）服務中斷、電腦損壞或系統故障；（3）替代品或服務的費用；（4）任何與本條款有關或與之有關的個人或身體傷害或情緒困擾而造成的任何損害；或（5）使用或無法使用該平台、專業服務或平台內容；（6）聯繫或接觸到任何與本平台的其他使用者或因使用本平台而交流或互動之其他人。 </li>
        </ol>
        <p><strong>第十七條：非承諾事項</strong></p>
        <ol>
          <li>本平台不是服務提供者、使用者或協力廠商之間任何協定成立下之任一方當事方。本平台所訂定之條款及規則或任何本平台內容的任何部分，包括但不限於任何計畫或其他服務，與使用者、消費者及服務提供者間並不構成代理、合夥、合資或就業等關係。本平台的任何成員或使用者均不得指揮或控制另一方的日常活動，或代表另一方承擔任何義務。本平台對於使用者的身份或背景，除有特別規定者外，不作任何確認或認可任何使用者或其聲稱的身份或背景，無論他們使用任何服務項目。 </li>
          <li>在本平台上對使用者以某種方式獲得許可或信任的任何引用僅僅表明使用者已完成相關帳戶過程或符合使用者評審標準，並不代表任何其他內容之正確性及真實性且本平台亦不因此負擔任何其他額外之給付義務。任何此類描述都不構成本平台之認證或擔保，亦非指本平台已對使用者身份核實，也並非針對服務提供者之服務項目是否具有執照、保險、可信、安全或合適做出確信。任何此類相關資訊，僅用作便於使用者通過本平台尋找專業服務時針對服務提供者之身份和適用性進行評估。 </li>
        </ol>
        <p><strong>第十八條：</strong><strong>損害賠償及責任上限</strong><strong> </strong></p>
        <ol>
          <li>使用者於使用本平台之相關服務時，如有違反相關規範、侵害本平台權利之情事，本平台保留一切法律追訴與求償之權利。 </li>
          <li>使用者於使用本平台之相關服務時，如有違反相關規範、侵害他人權利之情事，因而衍生或導致第三人向本平台提出索賠或請求時，使用者應負責承擔該訴訟，並賠償本平台、代表人、員工或其他被訴之人之一切損害。 </li>
          <li>不論於任何情況或任何情形之損害，本平台因法律上責任而賠償使用者之金額，均以該使用者於本平台內所支付之平台服務費總額為上限。 </li>
        </ol>
        <p><strong>第十九條：使用者之回饋及反應</strong></p>
        <ol>
          <li>關於針對本平台服務之任何回饋、評論、問題或建議（以下統稱回饋內容），使用者保證：（1）有權披露相關回饋內容；（2）回饋內容不侵犯任何其他人或實體的權利；（3）回饋內容不包含任何協力廠商或任何一方的機密或專有資訊。 </li>
          <li>藉由發送任何回饋內容之同時，使用者進一步：（1）同意本平台針對回饋內容沒有任何明示或暗示的保密義務；（2）授予本平台一個不可撤銷的、非獨家的、免版稅的、永久的、世界範圍的許可，藉以使用、修改、準備衍生作品、發佈、分發和授權回饋內容；（3）針對回饋內容同意放棄對本平台及其他使用者為任何道德權利的主張。 </li>
        </ol>
        <p><strong>第二十條：帳戶之暫停或終止</strong><strong>&nbsp;</strong></p>
        <ol>
          <li>本平台有權基於網站管理及其他正當合法考量，定期或不定期，暫時或永久修改或終止部分服務。本平台有權基於網站管理及其他正當合法考量，終止對多數或個別使用者之服務，包含但不限於：刪除超過一年未使用之帳號、凍結違反使用者服務條款之帳號、管理並刪除使用者於本平台內所發布之內容等。 </li>
          <li>如使用者違反本平台所制定之政策或條款及規則時，本平台可為以下任何或全部行為且無庸事先通知或解釋：（1）使用者之註冊帳戶將被停用或暫停，且該帳戶之密碼將被禁用，使用者將無法再行登入本平台，且使用者內容亦可能被移除；（2）於一定狀況下，本平台可通知其他使用者，告知說明該帳戶已被終止、阻塞、暫停、停用或取消，以及採取此項行動之原因；（3）消費者同意其無權因帳戶終止而取消或延遲支付服務費用給服務提供者；（4）本平台並不會補償服務提供者因為帳號終止所造成的任何損失，包含服務項目取消或延遲之損失。 </li>
          <li>使用者可以在任何時候取消對本平台之使用或自願終止已註冊之帳戶。如需終止帳戶請來聯繫客服：電話+886-2-2720-0449&nbsp;或email:&nbsp;help@bounbang.com </li>
        </ol>
        <p><strong>第二十一條：使用者間爭議</strong> <br>
        本平台重視所有的使用者，惟使用者間不免會發生爭執，由於本平台本身並不涉入使用者之間服務之履行，故使用者同意於服務提供者提供服務項目發生任何爭執事項時，使用者間得直接依中華民國法令於爭議管轄地法院提起調解或訴訟而為紛爭解決之機制。使用者皆承認並同意本平台於紛爭解決案件中，僅扮演資料提供之角色，而非為紛爭解決案件之一方當事人，故本平台沒有義務參與該紛爭解決案件並協助紛爭之解決。 </p>
        <p><strong>第二十二條：準據法及管轄法院</strong> <br>
          本使用者服務條款之解釋及適用，以中華民國法律為準據。使用者如有涉及法律問題而與本平台間產生爭議或糾紛，以台灣新竹地方法院為第一審管轄法院。 </p>
        <p><strong>第二十三條：其他約定條款</strong></p>
        <ol>
          <li>不可抗力：本平台及使用者無庸對另一方承擔因不可控制範圍所生之原因所產生的任何延遲或失敗的責任。這些原因包括但不限於火災、水災、地震、罷工、宣戰或未宣戰的戰爭、或國家災害等。 </li>
          <li>完整合意：本平台所發佈的任何其他法律聲明或附加條款或政策，將構成使用者和本平台關於使用本平台之完整合意內容。如任一條款之規定有無效之情形，該條款之無效情形不影響其餘條款的有效性。 </li>
          <li>連絡資訊：如果使用者對以上條款或本平台提供之服務有任何疑問，請發送電子郵件至以下信箱：&nbsp;support@bounbang.com；或者透過寫信郵遞至本平台，郵寄地址：新竹市北區四維路130號4F-2。 </li>
        </ol>
        <p>版本日期： 2019年&nbsp;4月30日 </p>
         <p>&nbsp;</p>
        <h5>智慧財產權政策</h5>
        <hr>
        <p>各位使用者您好，歡迎您註冊加入BounBang幫棒平台（下稱本平台），本平台致力於智慧財產權之尊重與保護，也希望我們的使用者也會給予同等之尊重。由於本平台並無法就所有本平台之所有內容與使用者發言一一做事前審查。如果您認為本平台之內容或使用者發言有侵害您的智慧財產權或相關權利，建議您可直接與發言者聯繫處理，或向本平台提出異議，本平台將於合法之權限範圍內，依據以下處理原則及規範，儘速為您處理。 </p>
        <p><strong>一、</strong>適用範圍：任何人認為本平台之內容或使用者發言有侵害智慧財產權或其他權利之情形時，得依本規範之內容，請求本平台處理。 <br>
          <strong>二</strong>、提出異議： </p>
        <ol>
          <li>聯絡方式：請以電子郵件寄送至support@bounbang.com。 </li>
          <li>異議內容：請提供以下資料，並請儘可能具體詳細說明之。您應瞭解，當您提供資料之不夠明確或無法判斷時，可能會造成被異議人在理解與判斷上之障礙，造成誤解，且拖延處理時間。          </li>
        </ol>
        <ul>
          <li>您的姓名與聯絡方式（電郵、手機）。 </li>
          <li>您認為侵權之作品或發言（名稱、發表日期、作者姓名、若僅為其中一段者請指名其所在段落） </li>
          <li>您認為您被侵害之權利與詳細說明。 </li>
          <li>您主張您具有前述權利之相關證明。 </li>
        </ul>
        <ol>
          <li>資料補正：您同意並瞭解，如您提供之資料不夠明確，經本平台初步審查認為需要補正，或被異議人要求補正時，本平台得要求您限期提供補充資料，您應配合提供，否則視同您未提出異議。 </li>
        </ol>
        <p><strong>三、</strong>異議流程： </p>
        <ol>
          <li>本平台接收到您的異議後，將於七個工作日（不含例假日）內進行初步審查。如認為需要補正時，應要求異議人提供補充資料；不須補正者，即將異議內容轉送被異議人。 </li>
          <li>本平台將於初審完成後，儘速將被異議內容予以暫時性下架、封鎖、移除或為內容之修正調整。 </li>
          <li>本平台將異議內容轉送被異議人時，將與異議人之個人資料一併轉送，以利被異議人得直接與異議人聯繫處理爭議，異議人同意本平台之上述個資利用行為。 </li>
          <li>本平台將異議內容轉送被異議人後，將指定一定期限，原則上為十個工作日（不含例假日），命被異議人提出處理之說明。處理期限得依個案情節之複雜程度延長或縮減之。 </li>
          <li>被異議人應於期限內，將處理情形回覆本平台： </li>
          <li>被異議人是否同意本平台之暫時性下架、封鎖、移除或內容之修正調整。 </li>
          <li>被異議人認為資料不足者，得要求補正資料（請具體指明何種資料）。 </li>
          <li>被異議人與異議人已達成協議者，其協議內容。 </li>
          <li>被異議人否認侵權。 </li>
          <li>前項被異議人否認侵權者，本平台將轉送被異議人之意見與異議人，異議人需於十個工作日（不含例假日）內，提出對被異議人提起侵權訴訟之證據，並檢送起訴證明予本平台。異議人未於上述期限提出起訴證據者，視同未提出異議，本平台將回復所有對被異議內容之暫時性下架、封鎖、移除或內容之修正調整。 </li>
          <li>為符合中華民國個人資料保護法之要求，對於前項之轉送意見，本平台僅轉送被異議人之否認與意見，不提供被異議人之任何個人資料。 </li>
        </ol>
        <p><strong>四、</strong>其他聲明： </p>
        <ol>
          <li>本平台僅就上述異議流程，提供轉達與聯繫，並為暫時性之下架、封鎖、移除或內容之修正調整。 </li>
          <li>本平台就爭議事實，無任何最終審判與裁量之權限，異議雙方無法達成協議者，應依循司法程序辦理，不得要求本平台介入處理。 </li>
          <li>異議人之異議不實者，應自負其責，如有不實而致生本平台或他人之損害者，應負損害賠償責任。 </li>
        </ol>
        <p>&nbsp;</p>
        <h5>隱私權政策</h5>
        <hr>
        <p>各位使用者您好，歡迎您註冊加入BounBang幫棒平台（下稱本平台），本平台極為尊重並且致力保護使用者之隱私，請使用者詳細閱讀本聲明，以瞭解使用者使用本平台與服務時，本平台處理使用者個人資料的政策與原則，以及蒐集利用之範圍及目的。當使用者使用本平台並將使用者的個人資訊提供給本平台時，即表示使用者接受及同意本隱私權聲明之內容。若使用者不願提供個人資料或不同意本聲明，請不要使用本平台及服務。本聲明隨時可能會修正變更，請隨時注意本平台之公告。 </p>
        <p><strong>一、</strong>適用範圍： <br>
          本聲明之適用範圍只限於本平台及本平台所提供之服務，對於其他聯結於本平台或網站之第三方服務，則非屬本平台所控制之範圍，使用者充分瞭解並同意，當使用者同意使用第三方服務時，將可能因第三方服務之要求，而提供必須之使用者個人資料予第三方，於此情形，蒐集、處理、利用個人資料之主體即為該第三方，應依該第三方之隱私權政策處理，與本平台無涉。 </p>
        <p><strong>二、</strong>告知事項： <br>
          ● 蒐集者名稱：誌瀚科技股份有限公司（即本平台）。 <br>
          ● 蒐集目的：本平台與使用者成立網站服務之契約關係，有因身分驗證、使用者管理與服務、信息聯繫、活動通知、數據分析、提供本平台相關服務、行銷、契約、類似契約或其他法律關係事務、其他經營合於營業登記項目或組織章程所定之業務客戶管理、使用者管理及其他與第三人合作之行銷推廣活動之必要，蒐集處理利用使用者之個人資料。 <br>
          ● 個人資料利用者：本平台、關係企業、服務提供者（在提供服務及與消費者聯繫之目的範圍內）及委外廠商（在提供委外服務之目的範圍內）。 <br>
          ● 個人資料利用方式：依蒐集目的範圍及本隱私權政策所載。 <br>
          ● 行使個人資料權利：依個人資料保護法第3條規定，使用者就使用者的個人資料享有查詢或請求閱覽、請求製給複製本、請求補充或更正、請求停止蒐集、處理或利用、請求刪除之權利。使用者可以透過本平台客服信箱support@bounbang.com行使上開權利，本平台將於收悉使用者的請求後，儘速處理。 </p>
        <p><strong>三、</strong>個人資料蒐集項目： <br>
          ● 使用者註冊基本資料，視個別具體情形，可能包括：姓名、身分證字號、性別、國籍、生日、電子信箱、帳號、付款資訊、社群網站帳號及相關資訊等資料，惟仍依實際情形為準。 <br>
          ● 一般瀏覽統計資料：為整體評估各個服務的流量、使用者進入本平台之路徑等資料，本平台會保留使用者在瀏覽網站時伺服器自行產生的相關記錄，包括使用者使用連線設備的IP位址、使用時間、使用的裝置、瀏覽及點選資料記錄等。 <br>
          ● 使用者活動記錄：使用者於本平台內之活動、瀏覽、發言記錄。 </p>
        <p><strong>四、</strong>注意事項： </p>
        <ol>
          <li>為提供更完善之使用者服務，本平台將透過蒐集、分析使用者使用服務之頻率、發言記錄、瀏覽偏好、點擊模式等等資料，瞭解使用者的使用需求，以便為使用者提供更個人化的服務與網站設計，作為本平台優化改善服務之參考。因此，本平台將可能對所蒐集之使用者個人資料，進行使用者行為總和分析或個體分析，惟前述研究均將在去個人化之前提下進行數據研究，此項去個人化之結果，將不能辨識也不會洩漏使用者的任何個人資料。 </li>
          <li>如因本平台或本平台被合併、收購或重整，或將部份或全部資產以任何形式銷售給第三方，而必需將使用者個人資料轉移時，本平台將會在資料轉移時通知使用者新適用的隱私權政策。 </li>
        </ol>
        <p><strong>五、</strong>個人資料之保護： </p>
        <ol>
          <li>本平台將嚴謹妥善保管使用者的個人資料，不會任意揭露或出售、交換、出租或轉讓使用者的任何非公開性資料予第三人，除非經由使用者的同意或法律之規定。 </li>
          <li>個人資料的安全防範：使用者的使用者帳號、密碼等任何個人資料，請不要提供給任何第三人。 </li>
          <li>本平台會盡最大努力確保儲存於系統中的安全性，本平台使用適當保全措施避免系統被非法侵入、防止資料被修改、揭露或破壞。由於網際網路資料的傳輸不能保證百分之百的安全，儘管本平台努力保護使用者的個人資料安全，仍無法確保使用者傳送或接收資料的安全，相關個人資料之風險並不在本平台承擔的責任範圍內。 </li>
          <li>以下情形，本平台有權向政府機關或其他相關單位提供使用者的個人資料： </li>
        </ol>
        <blockquote>
          <p>● 使用者同意資料公開或與第三方共同使用時。 <br>
            ● 為符合法律上的責任或政府機關的要求。 <br>
            ● 其他緊急情況，為保障其他使用者或公眾安全、公共利益時。 <br>
            ● 使用者在網站中之行為違反相關條款或使用規範、或有任何不法情事。 <br>
            ● 為追查或防止詐騙行為，或是解決本平台系統安全或其他技術上的問題。 <br>
            ● 在必要的狀況下，為偵查或防範可能發生之非法行為，或是避免任何違反使用者條款或隱私權政策之行為發生。 <br>
            ● 本平台可能會和第三方公司合作或委託提供使用者活動，當使用者出於自願參加這些活動時，本平台將可能依據第三方公司之設定或要求，將使用者之個人資料轉交第三方公司，所有使用者提供給第三方公司的個人資料會受該公司的規定及政策規範。 </p>
        </blockquote>
        <p><strong>六、</strong>電子郵件之使用： </p>
        <ol>
          <li>基於提供使用者服務資訊的需求，本平台可能傳送商業性資料或電子郵件給使用者。本平台將註明相關訊息是由本平台發送，也會提供使用者能隨時停止接收這些資料或電子郵件的方法、說明或功能連結。 </li>
          <li>本平台會不定期發送電子郵件給使用者有關本平台最新的一般服務訊息，或是第三方針對本平台使用者所提供的新產品及服務資訊。使用者有權選擇是否訂閱，當使用者想中止訂閱時，使用者可以登入至使用者中心自行取消訂閱，或是聯絡本平台客服。本平台將尊重使用者的選擇。 </li>
        </ol>
        <p><strong>七、</strong>請求刪除與修改： <br>
          使用者可隨時刪除或修改已登錄之個人資料，或要求本平台刪除所蒐集之個人資料，惟前述刪除、修改資料之要求，將可能使本平台無法對使用者正常提供相關服務，於此情形，本平台將先行通知使用者，如使用者仍要求刪除、修改資料，本平台有權終止對使用者之所有服務，並不負擔任何賠償責 </p>
        <p>&nbsp;</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">關閉視窗</button>
      
      </div>
    </div>
  </div>
</div>
<script>
new Vue({
  el: "#app",
  data: {
	email: '',
	user: {open_offer_setting:'',first_name:'',last_name:'',email:'',password:'',sex:'',phone_nat_code:'886',phone_number:'',usr_status:0},
	chk_password: '',
	old_email: '',
	is_tomail: '',
	existed: '',
	sending: '',
	next_sending: '',
	positions: '',
	citys: '',
	areas:'',
	areas: '',
	latlngs: [],
	agree: false,
	
  },
  mounted: function () {
	  var self = this;
	  axios.get('/api/get_profile').then(function (response){
		  console.log(response.data);
		  
		  if(response.data=='error')
			  alert('喔喔!錯誤了喔')
		  else
		  {
			  self.user = response.data.user;
			  self.old_email = response.data.user.email;
			  self.positions = response.data.positions;
			  self.citys = response.data.citys;
			  self.areas = response.data.areas;
			  
		  }
	  })
  },
  methods: {
		chk_mail: function(value){
			var mail = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
			return mail.test(value);
		},
		sendform: function(){
			var self = this;
			self.latlngs = [];
			var chk = 1;
			if(!self.agree && self.user.usr_status<1)
			{
				alert('請詳閱使用者條款，並點選同意方框，才能執行下一步驟。')
				chk = 0;
				$("body,html").scrollTop(0);	
			}
			if(!self.user.last_name)
			{
				$("#last_name").css({"border":"1px solid #a02"})
				chk = 0;
				$("body,html").scrollTop(0);
			}else
				$("#last_name").css({"border":"1px solid #8fc555"})
			
			if(!self.user.first_name)
			{
				$("#first_name").css({"border":"1px solid #a02"})
				chk = 0;
				$("body,html").scrollTop(0);
			}else
				$("#first_name").css({"border":"1px solid #8fc555"})
			
			if(!self.user.phone_number || isNaN(self.user.phone_number) || self.user.phone_number.length<8 || self.user.phone_number.length>12)
			{
				$("#phone_number").css({"border":"1px solid #a02"})
				chk = 0;
				$("body,html").scrollTop(100);
			}else
				$("#phone_number").css({"border":"1px solid #8fc555"})
				
			if(!self.user.email || !self.chk_mail(self.user.email))
			{
				$("#email").css({"border":"1px solid #a02"})
				chk = 0;
			}else
				$("#email").css({"border":"1px solid #8fc555"})
			
			if(self.user.password && self.chk_password && self.user.password!=self.chk_password)
			{
				$("#password").css({"border":"1px solid #a02"});
				$("#chk_password").css({"border":"1px solid #a02"})
				chk = 0;
			}else
			{
				$("#password").css({"border":"1px solid #8fc555"});
				$("#chk_password").css({"border":"1px solid #8fc555"})	
			}
			
			for(var i=0;i<self.positions.length;i++)
			{
				if(!self.positions[i].city)
				{
					$("#city"+i).css({"border":"1px solid #a02"});
					chk = 0;
				}else
					$("#city"+i).css({"border":"1px solid #8fc555"});
					
				if(!self.positions[i].nat)
				{
					$("#nat"+i).css({"border":"1px solid #a02"});
					chk = 0;
				}else
					$("#nat"+i).css({"border":"1px solid #8fc555"});
					
				if(!self.positions[i].addr)
				{
					$("#addr"+i).css({"border":"1px solid #a02"});
					chk = 0;
				}else
					$("#addr"+i).css({"border":"1px solid #8fc555"});		
				
			}
			
			if(chk)
			{
				self.next_sending = 1;	
				$("#mainFrm").submit();
			}
		},
		veri_mail: function(){
			var self = this;
			if(confirm('要執行Email認證作業?'))
			{
			  self.sending = 1;
			  axios.get('/api/set_veri_mail?id='+self.user.email).then(function (response){
				  console.log(response.data);
				  if(response.data.is_tomail)
					self.is_tomail = response.data.is_tomail;
				  
			  })
			}
		},
		is_existed: function(){
			var self = this;
			axios.get('/api/is_existed?id='+self.user.email).then(function (response){
				console.log(response.data);
				self.existed = response.data;
			})
		},
		select_city: function(x){
			var self = this;
			var index = self.citys.indexOf(self.positions[x].city);
			self.positions[x].areas = self.areas[index];
			self.positions[x].nat = '';
			self.positions[x].zip = '';
		},
		select_nat: function(x){
			var self = this;
			self.positions[x].zip  = self.positions[x].areas[self.positions[x].nat];
		},
		lessBtn: function(x){
			var self = this;
			self.positions.splice(x,1);
		},
		get_latlng: function(x){
			var self = this;
			var map_address = self.positions[x].city+self.positions[x].nat+self.positions[x].addr
			self.addressToLatLng(map_address,x);
		},
		addressToLatLng: function(addr,x) {
			var self = this;
			geocoder = new google.maps.Geocoder();
			geocoder.geocode({
				"address": addr
			}, 
			function (results, status) {
			  
			  if (status == google.maps.GeocoderStatus.OK) {
				  
				  self.positions[x].lat = results[0].geometry.location.lat();
				  self.positions[x].lng = results[0].geometry.location.lng();
			  }else {
				  //$("#target").val(content + addr + "查無經緯度" + "\n");
			  }
			});
		},
		add_address: function(){
			var self = this;
			self.positions.push({city:'',areas:[],nat:'',zip:'',addr:'',lat:'',lng:''});
		}
	
	}
  
})

$('.file-input').change(function(){
    var curElement = $(this).parent().parent().find('.image');
    console.log(curElement);
    var reader = new FileReader();

    reader.onload = function (e) {
        // get loaded data and render thumbnail.
        curElement.attr('src', e.target.result);
    };

    reader.readAsDataURL(this.files[0]);
});

var ms_ie = false;
var ua = window.navigator.userAgent;
var old_ie = ua.indexOf('MSIE ');
var new_ie = ua.indexOf('Trident/');

if ((old_ie > -1) || (new_ie > -1)) {
  ms_ie = true;
}

if (ms_ie) {
  document.documentElement.className += " ie";
}
</script>
@stop
