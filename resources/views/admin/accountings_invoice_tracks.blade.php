<!----發票字軌-------------------------------------------->
<div class="w-100" v-if="item=='invoice_tracks'">
    <div class="w-100 p-2 d-table">
        <a class="btn btn-primary pull-right" v-if="action!='create' && invoice_track" href="javascript:void(0)" @click="get_accountings()" v-text="'返回'"></a>
        <div class="w-50 float-left h4" v-if="action=='manage' && !invoice_track">
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="Year" @change="get_accountings" :value="today.getFullYear()-1911" v-model="Year" id="y1">
                <label class="form-check-label" for="y1" v-text="(today.getFullYear()-1911)+'年'"></label>
            </div>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="Year" @change="get_accountings" :value="today.getFullYear()-1910" v-model="Year" id="y2">
                <label class="form-check-label" for="y2" v-text="(today.getFullYear()-1910)+'年'"></label>
            </div>
        </div>
        <h3 class="float-right" v-text="title"></h3>
    </div>
    <div class="w-100" v-if="action=='create'">
        <form id="mainFrm"  action="/admin/accountings_pt" method="post">
          @csrf
          <input type="hidden" name="item" v-model="item" />
          <input type="hidden" name="action" v-model="action" />
          <table class="table table-light table-bordered" >
              <tr>
                <th class="w-25 text-center">發票年度</th>
                <td class="w-75">
                    <div class="w-100 p-1" id="YearDiv">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="Year" :value="today.getFullYear()-1911" v-model="invoice_track.Year" id="c" >
                            <label class="form-check-label" for="c" v-text="(today.getFullYear()-1911)+'年'"></label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="Year" :value="today.getFullYear()-1910" v-model="invoice_track.Year" id="h">
                            <label class="form-check-label" for="h" v-text="(today.getFullYear()-1910)+'年'"></label>
                        </div>
                    </div>
                 </td>
              </tr>
              <tr>
                <th class="w-25 text-center">發票期別</th>
                <td class="w-75">
                    <div class="w-100 p-1" id="TermDiv">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="Term" value="1" v-model="invoice_track.Term" id="t1" >
                            <label class="form-check-label" for="t1" v-text="'一、二月'"></label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="Term" value="2" v-model="invoice_track.Term" id="t2" >
                            <label class="form-check-label" for="t2" v-text="'三、四月'"></label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="Term" value="3" v-model="invoice_track.Term" id="t3" >
                            <label class="form-check-label" for="t3" v-text="'五、六月'"></label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="Term" value="4" v-model="invoice_track.Term" id="t4" >
                            <label class="form-check-label" for="t4" v-text="'七、八月'"></label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="Term" value="5" v-model="invoice_track.Term" id="t5" >
                            <label class="form-check-label" for="t5" v-text="'九、十月'"></label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="Term" value="6" v-model="invoice_track.Term" id="t6" >
                            <label class="form-check-label" for="t6" v-text="'十一、十二月'"></label>
                        </div>
                    </div>
                 </td>
              </tr>
              <tr>
                <th class="w-25 text-center">字軌英文代碼</th>
                <td class="w-75">
                    <input type="text" class="form-control text-center" @blur="upper" name="AphabeticLetter" id="AphabeticLetter" v-model="invoice_track.AphabeticLetter" placeholder="兩碼大寫英文" style="max-width:100px;" >
                 </td>
              </tr>
              <tr>
                <th class="w-25 text-center">發票號碼</th>
                <td class="w-75">
                    <div class="w-25 float-left">
                        <span class="text-primary">起始號碼</span>
                        <input type="text" class="w-100 form-control" name="StartNumber" id="StartNumber" v-model="invoice_track.StartNumber" placeholder="發票起始號碼 如：00000001" />
                    </div>
                    <div class="w-25 float-left ml-3">
                        <span class="text-primary">結束號碼</span>
                        <input type="text" class="w-100 form-control" name="EndNumber" id="EndNumber" v-model="invoice_track.EndNumber" placeholder="發票結束號碼 如：00009999
" />
                    </div>
                 </td>
              </tr>
              <tr>
                <th class="w-25 text-center">發票類別</th>
                <td class="w-75">
                    <div class="w-100 p-1" id="TypeDiv">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="Type" value="07" v-model="invoice_track.Type" id="c1" >
                            <label class="form-check-label" for="c1" v-text="'一般稅額'"></label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="Type" value="08" v-model="invoice_track.Type" id="h1">
                            <label class="form-check-label" for="h1" v-text="'特種稅額'"></label>
                        </div>
                    </div>
                 </td>
              </tr>
          </table>    
          <div class="w-100 py-3 text-center">
            <a href="javascript:void(0)" class="btn btn-primary" @click="sendFormAdd" v-text="'新增字軌'"></a>
          </div>
        </form>
    </div>
    <div class="w-100" v-if="action=='manage' && invoice_track.ManagementNo">
        <form id="mainFrm"  action="/admin/accountings_pt" method="post">
          @csrf
          <input type="hidden" name="item" v-model="item" />
          <input type="hidden" name="action" v-model="action" />
          <input type="hidden" name="Year" v-model="invoice_track.Year" />
          <input type="hidden" name="ManagementNo" v-model="invoice_track.ManagementNo" />
          <table class="table table-light table-bordered" >
              <tr>
                <th class="w-25 text-center">詳細資料</th>
                <td class="w-75">
                    <div class="w-100" v-text="'年度 : '+invoice_track.Year"></div>
                    <div class="w-100" v-text="'月份 : '+invoice_track.Term"></div>
                    <div class="w-100" v-text="'字軌英文代碼 : '+invoice_track.AphabeticLetter"></div>
                    <div class="w-100" v-text="'起始號碼 : '+invoice_track.StartNumber"></div>
                    <div class="w-100" v-text="'結束號碼 : '+invoice_track.EndNumber"></div>
               </td>
              </tr>
              
              <tr>
                <th class="w-25 text-center">字軌狀態</th>
                <td class="w-75">
                    <div class="w-100 p-1" id="FlagDiv">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="Flag" value="0" v-model="invoice_track.Flag" id="f1" >
                            <label class="form-check-label" for="f1" v-text="'暫停'"></label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="Flag" value="1" v-model="invoice_track.Flag" id="f2" >
                            <label class="form-check-label" for="f2" v-text="'啟用'"></label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="Flag" value="2" v-model="invoice_track.Flag" id="f3" >
                            <label class="form-check-label" for="f3" v-text="'停止'"></label>
                        </div>
                    </div>
                 </td>
              </tr>
          </table>    
          <div class="w-100 py-3 text-center">
            <a href="javascript:void(0)" class="btn btn-primary" @click="sendFormEdit" v-text="'修改字軌'"></a>
          </div>
        </form>
    </div>
    <div class="w-100" v-if="invoice_tracks && !invoice_track.ManagementNo">
        <table class="table table-light table-bordered table-hover" >
            <tr class="text-center bg-secondary text-white">
                <th>年度</th>
                <th>月份</th>
                <th>代碼</th>
                <th>起始編號</th>
                <th>結束編號</th>
                <th>剩餘數量</th>
                <th>稅別</th>
                <th>狀態</th>
            </tr>
            <tr class="text-center" v-for="(invoice_track,index) in invoice_tracks" @click="getThisData(index)" style="cursor:pointer">
                <td v-text="invoice_track.Year"></td>
                <td v-text="invoice_track.Term"></td>
                <td v-text="invoice_track.AphabeticLetter"></td>
                <td v-text="invoice_track.StartNumber"></td>
                <td v-text="invoice_track.EndNumber"></td>
                <td v-text="invoice_track.LastNumber"></td>
                <td v-text="invoice_track.Type"></td>
                <td v-html="invoice_track.status"></td>  
            </tr>
        </table>      	
    </div>
</div>
<script>
new Vue({
  el: "#app",
  data: {
	isBg: true,
	item: '<?php echo ((isset($item))?$item:'')?>',
	action: '<?php echo ((isset($action))?$action:'')?>',
	id: '<?php echo ((isset($id))?$id:'')?>',
	status: '<?php echo ((isset($status))?$status:'')?>',
	message: '<?php echo ((isset($message))?$message:'')?>',
	invoice_tracks: '',
	invoice_track: '',
	title: '',
	today: new Date(),
	old_Flag: '',
	Year: '<?php echo date("Y")-1911?>',
	
  },
  mounted: function () {
  	var self = this;
	if(self.message)
	{
		alert(self.message);
		self.message = '';
	}
	self.get_accountings();
  },
  methods: {
  	get_accountings: function(x){
		var self = this;
		self.isBg = true;
		axios.get('/admin/get_accountings?item='+self.item+'&Year='+self.Year+'&action='+self.action+'&id='+self.id).then(function (response){
			console.log(response.data)
			if(response.data=='error')
				window.location = '/error';
			
			self.invoice_tracks = response.data.invoice_tracks;
			self.invoice_track = response.data.single_invoice_track;
			
			self.title = response.data.title;
			
			if(self.message)
			{
				alert(self.message);
				self.message = '';
			}
			self.isBg = false;			
		});
	},
	sendFormAdd: function(){
		var self = this;
		var chk = 1;
		if(!self.invoice_track.Year)
		{
			$("#YearDiv").css({"border":"1px solid #a02"});
			chk = 0;
		}else
			$("#YearDiv").css({"border":"1px solid #eee"});
			
		if(!self.invoice_track.Term)
		{
			$("#TermDiv").css({"border":"1px solid #a02"});
			chk = 0;
		}else
			$("#TermDiv").css({"border":"1px solid #eee"});
			
		if(!self.invoice_track.AphabeticLetter || self.invoice_track.AphabeticLetter.length!=2)
		{
			$("#AphabeticLetter").css({"border":"1px solid #a02"});
			chk = 0;
		}else
		{
			$("#AphabeticLetter").css({"border":"1px solid #eee"}).val($("#AphabeticLetter").val().toUpperCase());
		}
		if(!self.invoice_track.StartNumber)
		{
			$("#StartNumber").css({"border":"1px solid #a02"});
			chk = 0;
		}else
			$("#StartNumber").css({"border":"1px solid #eee"});
			
		if(!self.invoice_track.EndNumber)
		{
			$("#EndNumber").css({"border":"1px solid #a02"});
			chk = 0;
		}else
			$("#EndNumber").css({"border":"1px solid #eee"});
			
		if(!self.invoice_track.Type)
		{
			$("#TypeDiv").css({"border":"1px solid #a02"});
			chk = 0;
		}else
			$("#TypeDiv").css({"border":"1px solid #eee"});	
			
		if(chk && confirm('確定要新增此筆資料?'))
		{
			$("#mainFrm").submit();						
		}	
	},
	sendFormEdit: function(){
		var self = this;
		var chk = 1;
		if(!self.invoice_track.Year)
		{
			$("#YearDiv").css({"border":"1px solid #a02"});
			chk = 0;
		}else
			$("#YearDiv").css({"border":"1px solid #eee"});
			
		if(chk && confirm('確定要修改此筆字軌資料?'))
		{
			if(self.invoice_track.Flag==2)
			{
				if(confirm('修改狀態為"停用"，將無法改為啟用，確定?'))
					$("#mainFrm").submit();		
			}else
				$("#mainFrm").submit();						
		}	
	},
	upper: function(){
		$("#AphabeticLetter").val($("#AphabeticLetter").val().toUpperCase());
	},
	getThisData: function(x){
		var self = this;
		self.invoice_track = self.invoice_tracks[x];
		self.old_Flag = self.invoice_tracks[x].Flag;
	}
	
  }
  
})
</script>
 