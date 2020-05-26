jQuery(document).ready(function($) {

alert('inqna')

// $(".learn_more").hover(function(){

// })

function g()
{
$("#searchform").css({'display':'none'})
// alert('inchi chi ashxatum')
$(".custom-logo").click(function(){
	location.href = 'http://localhost/wordpress';
	// alert('logo')
})
$(".custom-logo").parents("a").attr("href", "http://localhost/wordpress/")

}
g()



$(".ajax_button").click(function(){


$.ajax({

url:MyAjax.ajaxurl,
type:'post',
data:{
	action:'action1',
	val1:2,
	val2:9,
},
success:function(e){
// alert('ajax ashxatuma')
	
	console.log(e)
},



})


// setTimeout(f(), 2000)
// f()


	// alert('ajax_button')
})



$(".ajax_ok").click(function(){

// alert("ok")
$.ajax({

url:MyAjax.ajaxurl,
type:'post',
data:{
	action:'action2',
	value1:12,
	value2:94,
},
success:function(e){
// alert('ajax ashxatuma')
	// alert('oki ajax')
	console.log(e)
},



})


// setTimeout(f(), 2000)
// f()


	// alert('ajax_button')
})






// $(document).on('click','.delete',function(){
// 	var _this=this;

// 	// alert($(this).attr('data-id'))
// 				// alert($(_this).attr('data-id'))
// 	var x = $(_this).attr('data-id');

//   $.ajax({
	
// 	url:'http://localhost/wordpress/wp-admin/admin-ajax.php',
// 	type:'post',
// 	data:{
// 		action:'delete',
// 		v1:x,
// 	},
// 	success:function(e){
// 		console.log(e)
// 		$(_this).parents("tr").remove()
// 	},
	


//   })

// })

var name;
var email;
var _this;
var x;
$(document).on('click','.edit',function(){
	_this = this;
	// alert($(this).attr("data-id"))

	name = $(_this).parents("tr").find('.update_name').html();
	email = $(_this).parents("tr").find('.update_email').html();
// console.log(name)

	x = $(_this).attr("data-id");
	
	$(".modal_name").val(name)
	$(".modal_email").val(email)


})

$(document).on('click','.save',function(){
var modal_name = $(".modal_name").val()
var modal_email = $(".modal_email").val()

  $.ajax({
		
		url:'http://localhost/wordpress/wp-admin/admin-ajax.php',
		type:'post',
		data:{
			action:'edit',
			v1:x,
			v2:modal_name,
			v3:modal_email,

		},
		success:function(e){
			// alert(x+' '+name+' '+email)
			console.log(e)
			$(_this).parents("tr").find('.update_name').html($(".modal_name").val());
    	var filter = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;

    	 if (filter.test(modal_email)) {

   				modal_email.focus;
				$(_this).parents("tr").find('.update_email').html($(".modal_email").val());
					
			}
			else{
				alert("email դաշտում գրեք միայն email!")
			}
					
				},
	 	})


})



$(".watch_match").click(function(){
	alert()
})
 
// function ABOUT_THE_PHOENIX(){
// $("body").append(`<div class='text1'><span style='font-size:40px;font-weaght:bold'>ABOUT THE PHOENIX</span><br><br>
// Far far away, behind the word mountains, far from the countries Vokalia and Consonantia Even the all-powerful Pointing has no control about the blind texts it is an almost unorthographic life One day however a small line of blind text by the name of Lorem Ipsum decided to leave for the far World of Grammar.</div>`);

$(".text1").append(`<br><button class='watch_game'>Watch game</button>`);


// $(".vce-row-content").append(`<br><div class='div1'></div>`)

// }
// ABOUT_THE_PHOENIX()


function gg(){
	var x = $('.overlay').find('h1').eq(0).html()
	if (x=='Hello world!') {
		$('.overlay').find('h1').eq(0).empty().html('Home')
	}
	$('.overlay').find('li').remove()
	$(".entry-title").find("a").remove()
	$(".entry-content").find("p").remove()
	$(".entry-title").find("p").remove()
	$(".author").remove()
	$(".cat-links").remove()


	
$(".days").removeClass("time_left")
$(".hourse").removeClass("time_left")
$(".minutes").removeClass("time_left")
$(".secondes").removeClass("time_left")
}
gg()

$(".time_description").addClass("time_description1")
$(".time_description1").removeClass("time_description")
$(".days").after("<br>")
$(".hourse").after("<br>")
$(".minutes").after("<br>")
$(".secondes").after("<br>")

$(".niggaa").click(function(){
	// alert('niggaaaaaaaaaaa')
})

$(".ddd").after("<div class='niggaa'></div>")
$(".niggaa").eq(1).remove()






$(".ok").click(function(){
var i = 0;
alert(i)
i++
	var x = $(".user_name").val()
	var x1 = $(".user_surname").val()
	var x2 = $(".user_email").val()
	var x3 = $(".user_age").val()
	if (x=="" || x1=="" || x2=="" || x3=="") {
		alert("Լրացնել բոլոր դաշտերը!")
	}
else if (!isNaN(x3)) {
// alert(x+' '+x1+' '+x2+' '+x3)

	 $.ajax({
	
	url:'http://localhost/wordpress/wp-admin/admin-ajax.php',
	type:'post',
	data:{
		action:'signup',
		v:x,
		v1:x1,
		v2:x2,
		v3:x3,
	},
	success:function(e){
		console.log(e)
		alert(e)



	},
	


  })

}
else{
	alert("age դաշտում գրել միայն թիվ!")
}

})
