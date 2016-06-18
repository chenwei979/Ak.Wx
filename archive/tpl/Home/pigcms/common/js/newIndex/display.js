function viewImg_one(img){
  artDialog({title:'图片查看', content:'<img width="817" height="479" src="'+img+'" />', fixed:true});
  return false;
} 
function viewImg_two(img){
  artDialog({title:'图片查看', content:'<img width="817" height="479" src="'+img+'" />', fixed:true});
  return false;      
}
function sup(id){
  $.ajax({
    type:"post",
    url:"index.php?g=System&m=Extendset&a=sup&id="+id,
    datatype:"json",
    data:{
      id:id
    },
    success:function(sta){
        art.dialog.open('/',{lock:true,title:'浏览模板',width:'80%',height:'80%',yesText:'关闭',background: '#000',opacity: 0.45, close : function () {
          clear_display();
        }});
    }
  })       
}
function clear_display(){
  $.ajax({
    type:"post",
    url:"index.php?g=System&m=Extendset&a=clear",
    data:{
      id:1
    },
    success:function(sta){
    }
  })
}   