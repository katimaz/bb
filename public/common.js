
var date = new Date();
var year = date.getFullYear();
for(i = 2018; i <= year; i++){
    $('#year').append('<option value=' + i + '>' + i + '</option>');
}

$("select").each(function(){
    if($(this).data('selected')){
        $("option[value='"+$(this).data('selected')+"']",this).attr('selected','selected');
    }
});


function check_all(obj,cName)
{
    var checkboxs = document.getElementsByName(cName);
    for(var i=0;i<checkboxs.length;i++){checkboxs[i].checked = obj.checked;}
}

$("input:checkbox").each(function(){
    if($(this).data('checked').toString().search($(this).val()) > -1){
        $(this).attr('checked','checked');
    }
});
$("input:radio").each(function(){
    if($(this).data('checked').toString().search($(this).val()) > -1){
        $(this).attr('checked','checked');
    }
});