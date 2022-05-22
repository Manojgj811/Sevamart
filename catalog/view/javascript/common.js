function getURLVar(key) {
	var value = [];

	var query = String(document.location).split('?');

	if (query[1]) {
		var part = query[1].split('&');

		for (i = 0; i < part.length; i++) {
			var data = part[i].split('=');

			if (data[0] && data[1]) {
				value[data[0]] = data[1];
			}
		}

		if (value[key]) {
			return value[key];
		} else {
			return '';
		}
	}
}

$(document).ready(function() {
	// Highlight any found errors
	$('.text-danger').each(function() {
		var element = $(this).parent().parent();

		if (element.hasClass('form-group')) {
			element.addClass('has-error');
		}
	});

	// Currency
	$('#form-currency .currency-select').on('click', function(e) {
		e.preventDefault();

		$('#form-currency input[name=\'code\']').val($(this).attr('name'));

		$('#form-currency').submit();
	});

	// Language
	$('#form-language .language-select').on('click', function(e) {
		e.preventDefault();

		$('#form-language input[name=\'code\']').val($(this).attr('name'));

		$('#form-language').submit();
	});

	/* Search */
	$('#search input[name=\'search\']').parent().find('button').on('click', function() {
		var url = $('base').attr('href') + 'index.php?route=product/search';

	//	alert(url);
		var value = $('header #search input[name=\'search\']').val();

		if (value) {
			url += '&search=' + encodeURIComponent(value);
		}

		location = url;
	});

	$('#search input[name=\'search\']').on('keydown', function(e) {
		if (e.keyCode == 13) {
			$('header #search input[name=\'search\']').parent().find('button').trigger('click');
		}
	});

	// Menu
	$('#menu .dropdown-menu').each(function() {
		var menu = $('#menu').offset();
		var dropdown = $(this).parent().offset();

		var i = (dropdown.left + $(this).outerWidth()) - (menu.left + $('#menu').outerWidth());

		if (i > 0) {
			$(this).css('margin-left', '-' + (i + 10) + 'px');
		}
	});

	// Product List
	$('#list-view').click(function() {
		$('#content .product-grid > .clearfix').remove();

		$('#content .row > .product-grid').attr('class', 'product-layout product-list col-xs-12');
		$('#grid-view').removeClass('active');
		$('#list-view').addClass('active');

		localStorage.setItem('display', 'list');
	});

	// Product Grid
	$('#grid-view').click(function() {
		// What a shame bootstrap does not take into account dynamically loaded columns
		var cols = $('#column-right, #column-left').length;

		if (cols == 2) {
			$('#content .product-list').attr('class', 'product-layout product-grid col-lg-6 col-md-6 col-sm-12 col-xs-12');
		} else if (cols == 1) {
			$('#content .product-list').attr('class', 'product-layout product-grid col-lg-4 col-md-4 col-sm-6 col-xs-12');
		} else {
			$('#content .product-list').attr('class', 'product-layout product-grid col-lg-3 col-md-3 col-sm-6 col-xs-12');
		}

		$('#list-view').removeClass('active');
		$('#grid-view').addClass('active');

		localStorage.setItem('display', 'grid');
	});

	if (localStorage.getItem('display') == 'list') {
		$('#list-view').trigger('click');
		$('#list-view').addClass('active');
	} else {
		$('#grid-view').trigger('click');
		$('#grid-view').addClass('active');
	}

	// Checkout
	$(document).on('keydown', '#collapse-checkout-option input[name=\'email\'], #collapse-checkout-option input[name=\'password\']', function(e) {
		if (e.keyCode == 13) {
			$('#collapse-checkout-option #button-login').trigger('click');
		}
	});

	// tooltips on hover
	$('[data-toggle=\'tooltip\']').tooltip({container: 'body'});

	// Makes tooltips work on ajax generated content
	$(document).ajaxStop(function() {
		$('[data-toggle=\'tooltip\']').tooltip({container: 'body'});
	});
});

/////////////////////////////////////////////////////////////
        
        function cartUPDATE(id)
       {
           //alert('cartadd '+id);

          const currentId=id;
           
          var minsajax=$("#"+id+"productquantityspecial").val();
	
		//  alert( 'productquantityspecial is '+minsajax);


		  var minsajax2=$("#"+id+"productquantitylatest").val();

		// alert( 'productquantitylatest is '+minsajax2);

		  var minsajax3=$("#"+id+"productquantitybestseller").val();

		  //alert( 'productquantitybestseller is '+minsajax3);  

		  var minsajax4=$("#"+id+"productquantityfeatured").val();

		//  alert  ( 'productquantityfeatured is '+ minsajax4);

          let updatedValue1=minsajax-1;

		  let updatedValue2=minsajax2-1;

		  let updatedValue3=minsajax3-1;

		  let updatedValue4=minsajax4-1; 


        $("#"+id+"productquantityspecial").val(parseInt(updatedValue1));

		$("#"+id+"productquantitylatest").val(parseInt(updatedValue2));

		$("#"+id+"productquantitybestseller").val(parseInt(updatedValue3));

		$("#"+id+"productquantityfeatured").val(parseInt(updatedValue4));

		let updatedValue=(parseInt(updatedValue1)) || (parseInt(updatedValue2))  ||  ( parseInt(updatedValue3) ) || ( parseInt(updatedValue4) )  ;

		
         if(  (updatedValue1==0) || (updatedValue2==0)|| (updatedValue3==0)|| (updatedValue4==0))

	//	if( updatedValue1)
          { 

			//alert('update is '+updatedValue1);
              $('#'+currentId+"autospecial").hide(); 

			  $('#'+currentId+"autolatest").hide(); 

			  $('#'+currentId+"autoproductcategory").hide();

			  $('#'+currentId+"autofeatured").hide();

			  $('#'+currentId+"autobestseller").hide();  

			  $('#'+currentId+"autoproductproduct").hide();  

			  $('#'+currentId+"autoproductsearch").hide();

			  $('#'+currentId+"autoproductSpecial").hide();

			  $('#'+currentId+"autoproductmanufacture").hide(); 

              $.ajax({
              url: 'index.php?route=checkout/cart/remove',
			 type: 'post',
			 data: 'key=' + id ,
			 dataType: 'json',
          
         success: function(json) {
   
            if(json['success']) {

               $('#cart > button').html('<span id="cart-total"><i class="fa fa-shopping-cart"></i> ' + json['total'] + '</span>');

            	$('#cart > ul').load('index.php?route=common/cart/info ul li');

               }
 
			   $('#'+id+"latest").show(); 

			   $('#'+id+"special").show();  
			   
			 //  $('#'+id+"loadspecial").show();  

			   $('#'+id+"productcategory").show();

			   $('#'+id+"productproduct").show();

			   $('#'+id+"bestseller").show();

			   $('#'+id+"featured").show();

			   $('#'+id+"search").show();

			   $('#'+id+"productSpecial").show();

			   $('#'+id+"autoproductmanufacture").show();



                }
                
			});

          }
		

          if(updatedValue==0|| updatedValue)
          {
			//let updatedValue=(parseInt(plusajax)+1) || (parseInt(plusajax2)+1)  ||  (parseInt(plusajax3)+1) || (parseInt(plusajax4)+1)  ;

          let input = document.querySelector(".input");  

    // alert('  minus  updatedValue  value is '+updatedValue);
     
          let textbox=$("#"+id+"productquantityspecial").val();

		  let textbox2=$("#"+id+"productquantitylatest").val();

		  let textbox3=$("#"+id+"productquantityfeatured").val();

		  let textbox4=$("#"+id+"productquantitybestseller").val();

		 //alert('textbox is abc'+textbox); 	 
		  
		 // alert('textbox is abc'+textbox2);

	      $.ajax({
            url: 'index.php?route=checkout/cart/update',
            type: 'post',
            data: 'product_id=' + id + '&quantity=' + updatedValue,
            dataType: 'json',
          
            success: function(json) {

            if(json['success']) {

           var productMinimum=json['minimum'];

              //alert("minimum of this product is"+productMinimum);

            if (  parseInt( updatedValue) <json['minimum'] )
            {
			
		//	alert("minimum of this product is"+productMinimum);

              $('#'+currentId+"autospecial").hide(); 

			  $('#'+currentId+"autolatest").hide(); 

			  $('#'+currentId+"autoproductcategory").hide();

			  $('#'+currentId+"autoproductproduct").hide();  

			  $('#'+currentId+"autofeatured").hide();

			  $('#'+currentId+"autobestseller").hide();

			   $('#'+currentId+"autoproductsearch").hide();

			   $('#'+currentId+"autoproductSpecial").hide();

			   $('#'+currentId+"autoproductmanufacture").hide();

               $.ajax({
                url: 'index.php?route=checkout/cart/remove',
                type: 'post',
                data: 'key=' + id ,
                dataType: 'json',

           success: function(json) {
      
            if(json['success'])
            {
                $('#cart > button').html('<span id="cart-total"><i class="fa fa-shopping-cart"></i> ' + json['total'] + '</span>');

               $('#cart > ul').load('index.php?route=common/cart/info ul li');

            }


              $('#'+id+"latest").show(); 

			  $('#'+id+"special").show();

			  $('#'+id+"productcategory").show();  

			 $('#'+id+"productproduct").show();

			  $('#'+id+"featured").show();

			 $('#'+id+"bestseller").show();

			 $('#'+id+"search").show();

			  $('#'+id+"productSpecial").show();

			 $('#'+id+"productmanufacture").show();


                  }
                   
              });

              }

           else
             {

				 $('#cart > button').html('<span id="cart-total"><i class="fa fa-shopping-cart"></i> ' + json['total'] + '</span>');
         
          	$('#cart > ul').load('index.php?route=common/cart/info ul li');

            }

            }
      
            }


           });

           }

         }
                  

		 function cartAdd(id)
		 {
		       //alert('id is '+id);
	  
			var  plusajax=$("#"+id+"productquantityspecial").val();

			//alert('  productquantityspecial  '+plusajax);  alert('  productquantitybestseller  '+plusajax4);

			var  plusajax2=$("#"+id+"productquantitylatest").val();

		var  plusajax3=$("#"+id+"productquantityfeatured").val();

			var  plusajax4=$("#"+id+"productquantitybestseller").val();

			let updatedValue=(parseInt(plusajax)+1) || (parseInt(plusajax2)+1)  ||  (parseInt(plusajax3)+1) || (parseInt(plusajax4)+1)  ;
 
	   
 
		  if(updatedValue)
		   {
			  //  alert('updatedvalue is '+updatedValue );

				$("#"+id+"productquantityspecial").val(parseInt(plusajax)+1) ;

				$("#"+id+"productquantitylatest").val(parseInt( updatedValue));

				$("#"+id+"productquantityfeatured").val(parseInt(updatedValue));

				$("#"+id+"productquantitybestseller").val(parseInt(updatedValue));
 

				//$("#"+id+"productquantityspecial").val(parseInt(plusajax)+1);

				//$("#"+id+"productquantitylatest").val(parseInt(plusajax2)+1);
	   
				//$("#"+id+"productquantityfeatured").val(parseInt(plusajax3)+1);
	   
				//$("#"+id+"productquantitybestseller").val(parseInt(plusajax4)+1);
			 
			 $.ajax({
			 url: 'index.php?route=checkout/cart/update',
			 type: 'post',
			 data: 'product_id=' + id + '&quantity=' + updatedValue,
			 dataType: 'json',
			 
			 success: function(json) { 
 
			  // alert('success is '+id);
		 
			 if(json['success']) 
			 {
 
	          $('#cart > button').html('<span id="cart-total"><i class="fa fa-shopping-cart"></i> ' + json['total'] + '</span>');
 
			   $('#cart > ul').load('index.php?route=common/cart/info ul li');
 
				}

				
			}
 
			  });
 
			 }
 
	}



///// proucts , special ,latest ,feature add to cart

		$(document).ready(function() {

        $('.button-cart').on('click', function() {

		let varc=this.id;

		//alert('varc is '+varc);

		//	$('#'+varc).hide();

		 $('#'+varc+'special').hide();

		 $('#'+varc+'loadspecial').hide();

		 $('#'+varc+'latest').hide();

		 $('#'+varc+'productcategory').hide();

		 $('#'+varc+'productproduct').hide();

		 $('#'+varc+'productSpecial').hide();

		 $('#'+varc+'bestseller').hide();

		 $('#'+varc+'featured').hide();

		 $('#'+varc+'search').hide();

		 $('#'+varc+'productmanufacture').hide();

		$.ajax({
		url: 'index.php?route=checkout/cart/add',
		type: 'post',
		data: 'product_id=' + this.id + '&quantity=' + $("#"+this.id+"product").val(),
		dataType: 'json',
		beforeSend: function() 
		{
			$(varc).button('loading');
		},
		complete: function() {
			$(varc).button('reset');
		},

          success: function(json) 
			{

			if (json['redirect']) 
			{
				location = json['redirect'];
			
			}

		if(json['producttotal'] )
		{
			var b=json['producttotal'];

			var cartajaxid=json['productID'];

			// alert('cartajaxid is '+cartajaxid);

			var quantityProduct=json['productQuantity'];  

			//alert('productQuantity is '+quantityProduct);

			var productMinimum=json['minimum'];  

			var max=json['maximumquantity'];

			/*
			$('#'+varc).after('<div class="input-group" id="'+json['productID']+'autoproductcategory"  style="margin-left: 0px;"  >'+
			'<span class="input-group-btn">'+'<button  class="dec qtybtn btn btn-default btn-number minus" id="'+json['productID']+'"  data-toggle="tooltip"   title="decrement" style="margin-top:0px; height: 40px; border: 0px solid #cccccc;"   onclick="cartUPDATE('+json['productID']+');")"><span class="glyphicon glyphicon-minus"></span></button>'+'</span>'+
			'<input  type="number" style ="margin-left:3px;  margin-top:0px; text-align: center; "   name="quantity"   value="'+json['producttotal']+'" min="'+json['minimum']+'"   id="'+json['productID']+'productquantity" class="input form-control addcartnumber "  disabled/>'+
			'<span class="input-group-btn">'+'<button  class="incre qtybtn btn btn-default"  id="'+json['productID']+'"  data-toggle="tooltip"   title="increment" style="margin-top:0px; height: 40px; border: 0px solid #cccccc;" onclick="cartAdd('+json['productID']+');"><span class="glyphicon glyphicon-plus"></span></button>'+'</span>'+
			'<div>'+' <button type="button" class="wishlist heart" data-toggle="tooltip" title="{{ button_wishlist }}" style="border-left: 1px solid #ddd; width: 50%;" onclick="wishlist.add('+json['productID']+');"><i class="fa fa-heart"></i></button>'+
			'<button type="button"  class="compare cmp" data-toggle="tooltip" title="{{ button_compare }}" style="border-left: 1px solid #ddd; width: 50%;"   onclick="compare.add('+json['productID']+');"><i class="fa fa-exchange"></i></button>'+'</div>'+
			'</div>'
			
			);

			*/

					$(`#${varc}latest`).after('<div class="input-group" id="'+json['productID']+'autolatest"  style="margin-left: 0px;"  >'+
					'<span class="input-group-btn">'+'<button  class="dec qtybtn btn btn-default btn-number minus" id="'+json['productID']+'"  data-toggle="tooltip"   title="decrement" style="margin-top:0px; height: 40px; border: 0px solid #cccccc;"   onclick="cartUPDATE('+json['productID']+');")"><span class="glyphicon glyphicon-minus"></span></button>'+'</span>'+
					'<input  type="number" style ="margin-left:3px;  margin-top:0px; text-align: center; "   name="quantity"   value="'+json['producttotal']+'" min="'+json['minimum']+'"   id="'+json['productID']+'productquantitylatest" class="input form-control addcartnumber "  disabled/>'+
					'<span class="input-group-btn">'+'<button  class="incre qtybtn btn btn-default"  id="'+json['productID']+'"  data-toggle="tooltip"   title="increment" style="margin-top:0px; height: 40px; border: 0px solid #cccccc;" onclick="cartAdd('+json['productID']+');"><span class="glyphicon glyphicon-plus"></span></button>'+'</span>'+
					'<div>'+' <button type="button" class="wishlist heart" data-toggle="tooltip" title="Add to Wishlist" style="border-left: 1px solid #ddd; width: 50%;" onclick="wishlist.add('+json['productID']+');"><i class="fa fa-heart"></i></button>'+
					'<button type="button"  class="compare cmp" data-toggle="tooltip" title="Add to compare" style="border-left: 1px solid #ddd; width: 50%;"   onclick="compare.add('+json['productID']+');"><i class="fa fa-exchange"></i></button>'+'</div>'+
					'</div>'
					
					);

		$(`#${varc}special`).after('<div class="input-group" id="'+json['productID']+'autospecial"  style="margin-left: 0px;"  >'+
		'<span class="input-group-btn">'+'<button  class="dec qtybtn btn btn-default btn-number minus" id="'+json['productID']+'"  data-toggle="tooltip"   title="decrement" style="margin-top:0px; height: 40px; border: 0px solid #cccccc;"   onclick="cartUPDATE('+json['productID']+');")"><span class="glyphicon glyphicon-minus"></span></button>'+'</span>'+
		'<input  type="number" style ="margin-left:3px;  margin-top:0px; text-align: center; "   name="quantity"   value="'+json['producttotal']+'" min="'+json['minimum']+'"   id="'+json['productID']+'productquantityspecial" class="input form-control addcartnumber "  disabled/>'+
		'<span class="input-group-btn">'+'<button  class="incre qtybtn btn btn-default"  id="'+json['productID']+'"  data-toggle="tooltip"   title="increment" style="margin-top:0px; height: 40px; border: 0px solid #cccccc;" onclick="cartAdd('+json['productID']+');"><span class="glyphicon glyphicon-plus"></span></button>'+'</span>'+
		'<div>'+' <button type="button" class="wishlist heart" data-toggle="tooltip" title="Add to Wishlist" style="border-left: 1px solid #ddd; width: 50%;" onclick="wishlist.add('+json['productID']+');"><i class="fa fa-heart"></i></button>'+
		'<button type="button"  class="compare cmp" data-toggle="tooltip" title="Add to compare" style="border-left: 1px solid #ddd; width: 50%;"   onclick="compare.add('+json['productID']+');"><i class="fa fa-exchange"></i></button>'+'</div>'+
		'</div>'
		
		);


			$('#'+varc+'featured').after('<div class="input-group" id="'+json['productID']+'autofeatured"  style="margin-left: 0px;"  >'+
		'<span class="input-group-btn">'+'<button  class="dec qtybtn btn btn-default btn-number minus" id="'+json['productID']+'"  data-toggle="tooltip"   title="decrement" style="margin-top:0px; height: 40px; border: 0px solid #cccccc;"   onclick="cartUPDATE('+json['productID']+');")"><span class="glyphicon glyphicon-minus"></span></button>'+'</span>'+
		'<input  type="number" style ="margin-left:3px;  margin-top:0px; text-align: center; "   name="quantity"   value="'+json['producttotal']+'" min="'+json['minimum']+'"   id="'+json['productID']+'productquantityfeatured" class="input form-control addcartnumber "  disabled/>'+
		'<span class="input-group-btn">'+'<button  class="incre qtybtn btn btn-default"  id="'+json['productID']+'"  data-toggle="tooltip"   title="increment" style="margin-top:0px; height: 40px; border: 0px solid #cccccc;" onclick="cartAdd('+json['productID']+');"><span class="glyphicon glyphicon-plus"></span></button>'+'</span>'+
		'<div>'+' <button type="button" class="wishlist heart" data-toggle="tooltip" title="Add to Wishlist" style="border-left: 1px solid #ddd; width: 50%;" onclick="wishlist.add('+json['productID']+');"><i class="fa fa-heart"></i></button>'+
		'<button type="button"  class="compare cmp" data-toggle="tooltip" title="Add to compare " style="border-left: 1px solid #ddd; width: 50%;"   onclick="compare.add('+json['productID']+');"><i class="fa fa-exchange"></i></button>'+'</div>'+
		'</div>'
		
					);

			$('#'+varc+'bestseller').after('<div class="input-group" id="'+json['productID']+'autobestseller"  style="margin-left: 0px;"  >'+
			'<span class="input-group-btn">'+'<button  class="dec qtybtn btn btn-default btn-number minus"  id="'+json['productID']+'auto"     data-toggle="tooltip"   title="decrement" style="margin-top:0px; height: 40px; border: 0px solid #cccccc;"   onclick="cartUPDATE('+json['productID']+');")"><span class="glyphicon glyphicon-minus"></span></button>'+'</span>'+
			'<input  type="number" style ="margin-left:3px;  margin-top:0px; text-align: center; "   name="quantity"   value="'+json['producttotal']+'" min="'+json['minimum']+'"   id="'+json['productID']+'productquantitybestseller" class="input form-control addcartnumber "  disabled/>'+
			'<span class="input-group-btn">'+'<button  class="incre qtybtn btn btn-default"  id="'+json['productID']+'"  data-toggle="tooltip"   title="increment" style="margin-top:0px; height: 40px; border: 0px solid #cccccc;" onclick="cartAdd('+json['productID']+');"><span class="glyphicon glyphicon-plus"></span></button>'+'</span>'+
			'<div>'+' <button type="button" class="wishlist heart" data-toggle="tooltip" title="Add to Wishlist " style="border-left: 1px solid #ddd; width: 50%;" onclick="wishlist.add('+json['productID']+');"><i class="fa fa-heart"></i></button>'+
			'<button type="button"  class="compare cmp" data-toggle="tooltip" title="Add to compare product" style="border-left: 1px solid #ddd; width: 50%;"   onclick="compare.add('+json['productID']+');"><i class="fa fa-exchange"></i></button>'+'</div>'+
			'</div>'
			
			);

			
		/*
		
		 $('#'+varc+'productcategory').before(`<div class="input-group"  id="${json['productID']}autoproductcategory">
		 <button  class="dec qtybtn btn btn-default btn-number minus"  id="${json['productID']}auto"  data-toggle="tooltip"   title="decrement"   style="margin-top:70px; margin-left:2px;  width:46px"   onclick="cartUPDATE(${json['productID']});" >
		 <i class="glyphicon glyphicon-minus"></i></button><span class="input-group-btn"><input type="number"  name="quantity" value="${json['producttotal']}" min="${json['minimum']}"  id="${json['productID']}productquantityspecial" class="input form-control "  style="margin-top:70px; margin-left:-195px; width:104px ; "  disabled/>
		 <button  class="incre qtybtn btn btn-default"  id="${json['productID']}"  data-toggle="tooltip"   title="increment"   style="margin-top:66px; margin-left:-107px; width:45px"  onclick="cartAdd(${json['productID']});"  ><i class="glyphicon glyphicon-plus"></i></button>
		 <button class="wishlist heart" data-toggle="tooltip" title=""  style="margin-top:65px; margin-left:-45px; width:32px"  onclick="wishlist.add(${json['productID']});"   ><i class="fa fa-heart"></i></button>
		 <button   class="compare cmp" data-toggle="tooltip" title=""    style="margin-top:65px; margin-left:-14px; width:31px"   onclick="compare.add(${json['productID']});"    ><i class="fa fa-exchange"></i></button></span></div>`);
	
     

  

		 $('#'+varc+'productproduct').before(`<div class="input-group"  id="${json['productID']}autoproductproduct">
		 <button  class="dec qtybtn btn btn-default btn-number minus"  id="${json['productID']}auto"  data-toggle="tooltip"   title="decrement"   style="width:35px;"   onclick="cartUPDATE(${json['productID']});" >
		 <i class="glyphicon glyphicon-minus"></i></button><span class="input-group-btn"><input type="number"  name="quantity" value="${json['producttotal']}" min="${json['minimum']}"  id="${json['productID']}productquantityspecial" class="input form-control "    style="width:188px;"/>   disabled/>
		 <button  class="incre qtybtn btn btn-default"  id="${json['productID']}"  data-toggle="tooltip"   title="increment"   style="margin-left:0.5px; width:35px;     margin-top: 1px;"  onclick="cartAdd(${json['productID']});"  ><i class="glyphicon glyphicon-plus"></i></button>
		
		</span></div>`);

		$('#'+varc+'search').before(`<div class="input-group"  id="${json['productID']}autoproductsearch">
		 <button  class="dec qtybtn btn btn-default btn-number minus"  id="${json['productID']}auto"  data-toggle="tooltip"   title="decrement"   style="margin-top:70px; margin-left:2px;  width:46px"   onclick="cartUPDATE(${json['productID']});" >
		 <i class="glyphicon glyphicon-minus"></i></button><span class="input-group-btn"><input type="number"  name="quantity" value="${json['producttotal']}" min="${json['minimum']}"  id="${json['productID']}productquantityspecial" class="input form-control "  style="margin-top:70px; margin-left:-195px; width:104px ; "  disabled/>
		 <button  class="incre qtybtn btn btn-default"  id="${json['productID']}"  data-toggle="tooltip"   title="increment"   style="margin-top:66px; margin-left:-107px; width:45px"  onclick="cartAdd(${json['productID']});"  ><i class="glyphicon glyphicon-plus"></i></button>
		 <button class="wishlist heart" data-toggle="tooltip" title="{{ button_wishlist }}"  style="margin-top:65px; margin-left:-45px; width:32px"  onclick="wishlist.add(${json['productID']});"   ><i class="fa fa-heart"></i></button>
		 <button   class="compare cmp" data-toggle="tooltip" title="{{ button_compare }}"    style="margin-top:65px; margin-left:-14px; width:31px"   onclick="compare.add(${json['productID']});"    ><i class="fa fa-exchange"></i></button></span></div>`);
	
		 $('#'+varc+'productSpecial').before(`<div class="input-group"  id="${json['productID']}autoproductSpecial">
		 <button  class="dec qtybtn btn btn-default btn-number minus"  id="${json['productID']}auto"  data-toggle="tooltip"   title="decrement"   style="margin-top:70px; margin-left:2px;  width:46px"   onclick="cartUPDATE(${json['productID']});" >
		 <i class="glyphicon glyphicon-minus"></i></button><span class="input-group-btn"><input type="number"  name="quantity" value="${json['producttotal']}" min="${json['minimum']}"  id="${json['productID']}productquantityspecial" class="input form-control "  style="margin-top:70px; margin-left:-195px; width:104px ; "  disabled/>
		 <button  class="incre qtybtn btn btn-default"  id="${json['productID']}"  data-toggle="tooltip"   title="increment"   style="margin-top:66px; margin-left:-107px; width:45px"  onclick="cartAdd(${json['productID']});"  ><i class="glyphicon glyphicon-plus"></i></button>
		 <button class="wishlist heart" data-toggle="tooltip" title="{{ button_wishlist }}"  style="margin-top:65px; margin-left:-45px; width:32px"  onclick="wishlist.add(${json['productID']});"   ><i class="fa fa-heart"></i></button>
		 <button   class="compare cmp" data-toggle="tooltip" title="{{ button_compare }}"    style="margin-top:65px; margin-left:-14px; width:31px"   onclick="compare.add(${json['productID']});"    ><i class="fa fa-exchange"></i></button></span></div>`);
	

		 $('#'+varc+'productmanufacture').before(`<div class="input-group"  id="${json['productID']}autoproductmanufacture">
		 <button  class="dec qtybtn btn btn-default btn-number minus"  id="${json['productID']}auto"  data-toggle="tooltip"   title="decrement"   style="margin-top:70px; margin-left:2px;  width:46px"   onclick="cartUPDATE(${json['productID']});" >
		 <i class="glyphicon glyphicon-minus"></i></button><span class="input-group-btn"><input type="number"  name="quantity" value="${json['producttotal']}" min="${json['minimum']}"  id="${json['productID']}productquantityspecial" class="input form-control "  style="margin-top:70px; margin-left:-195px; width:104px ; "  disabled/>
		 <button  class="incre qtybtn btn btn-default"  id="${json['productID']}"  data-toggle="tooltip"   title="increment"   style="margin-top:66px; margin-left:-107px; width:45px"  onclick="cartAdd(${json['productID']});"  ><i class="glyphicon glyphicon-plus"></i></button>
		 <button class="wishlist heart" data-toggle="tooltip" title="{{ button_wishlist }}"  style="margin-top:65px; margin-left:-45px; width:32px"  onclick="wishlist.add(${json['productID']});"   ><i class="fa fa-heart"></i></button>
		 <button   class="compare cmp" data-toggle="tooltip" title="{{ button_compare }}"    style="margin-top:65px; margin-left:-14px; width:31px"   onclick="compare.add(${json['productID']});"    ><i class="fa fa-exchange"></i></button></span></div>`);
	 


		 */

	  }

				$('.alert-dismissible, .text-danger').remove();
				$('.form-group').removeClass('has-error');

	   if (json['error']) {
		   if (json['error']['option']) {
			   for (i in json['error']['option']) {
				   var element = $('#input-option' + i.replace('_', '-'));

				   if (element.parent().hasClass('input-group')) {
					   element.parent().after('<div class="text-danger">' + json['error']['option'][i] + '</div>');
				   } else {
					   element.after('<div class="text-danger">' + json['error']['option'][i] + '</div>');
				   }
			   }
		   }

		   if (json['error']['recurring']) {
			   $('select[name=\'recurring_id\']').after('<div class="text-danger">' + json['error']['recurring'] + '</div>');
		   }

		   // Highlight any found errors
		   $('.text-danger').parent().addClass('has-error');
	   }

		if (json['success']) 
		{
			$('.breadcrumb').after('<div class="alert alert-success alert-dismissible">' + json['success'] + '<button type="button" class="close" data-dismiss="alert">&times;</button></div>');

			$('#cart > button').html('<span id="cart-total"><i class="fa fa-shopping-cart"></i> ' + json['total'] + '</span>');

			$('#cart > ul').load('index.php?route=common/cart/info ul li');
		
			}

        },
   
		error: function(xhr, ajaxOptions, thrownError) 
		{
			alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
		}

		    	});

    		});

   	});
//--></script> 

		


	

// Cart add remove functions


		var cart = {
	
	'add': function(product_id, quantity) {
		
		//alert('the var is'+product_id);
		    $.ajax({
			url: 'index.php?route=checkout/cart/add',
			type: 'post',
			data: 'product_id=' + product_id + '&quantity=' + (typeof(quantity) != 'undefined' ? quantity : 1),
			dataType: 'json',
			beforeSend: function() {
				$('#cart > button').button('loading');
			},
			complete: function() {
				$('#cart > button').button('reset');
			},
			success: function(json) {
				
				$('.alert-dismissible, .text-danger').remove();

				if (json['redirect']) {
					location = json['redirect'];
				}

				if (json['success']) {
					$('#content').parent().before('<div class="alert alert-success alert-dismissible"><i class="fa fa-check-circle"></i> ' + json['success'] + ' <button type="button" class="close" data-dismiss="alert">&times;</button></div>');

					// Need to set timeout otherwise it wont update the total
					setTimeout(function () {
						$('#cart > button').html('<span id="cart-total"><i class="fa fa-shopping-cart"></i> ' + json['total'] + '</span>');
					}, 300);

				//	$('html, body').animate({ scrollTop: 900 }, 'slow');

					$('#cart > ul').load('index.php?route=common/cart/info ul li');
				}
			},
			error: function(xhr, ajaxOptions, thrownError) {
				alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
			}
		});
	},
	'update': function(key, quantity) {

	//	alert('update');
		$.ajax({
			url: 'index.php?route=checkout/cart/edit',
			type: 'post',
			data: 'key=' + key + '&quantity=' + (typeof(quantity) != 'undefined' ? quantity : 1),
			dataType: 'json',
			beforeSend: function() {
				$('#cart > button').button('loading');
			},
			complete: function() {
				$('#cart > button').button('reset');
			},
			success: function(json) {
				// Need to set timeout otherwise it wont update the total
				setTimeout(function () {
					$('#cart > button').html('<span id="cart-total"><i class="fa fa-shopping-cart"></i> ' + json['total'] + '</span>');
				}, 100);

				if (getURLVar('route') == 'checkout/cart' || getURLVar('route') == 'checkout/checkout') {
					location = 'index.php?route=checkout/cart';
				} else {
					$('#cart > ul').load('index.php?route=common/cart/info ul li');
				}
			},
			error: function(xhr, ajaxOptions, thrownError) {
				alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
			}
		});
	},
	'remove': function(key) {

	//	alert(' global key is '+key);

		$('#'+key+"autolatest").hide(); 

		$('#'+key+"auto").hide(); 

		$("#idlabel").hide(); 

		//$('#'+key+"productquantity").val(''); 

		$('#'+key+"autospecial").hide();

		$('#'+key+"autoproductcategory").hide(); 

		$('#'+key+"autoproductproduct").hide(); 

		$('#'+key+"autobestseller").hide();

		$('#'+key+"autofeatured").hide();

		$('#'+key+"autoproductsearch").hide();  

		$('#'+key+"autoproductSpecial").hide(); 

		$('#'+key+"autoproductmanufacture").hide(); 

		
		$.ajax({
			url: 'index.php?route=checkout/cart/remove',
			type: 'post',
			data: 'key=' + key,
			dataType: 'json',
			beforeSend: function() {
				$('#cart > button').button('loading');
			},
			complete: function() {
				$('#cart > button').button('reset');
			},
			success: function(json) {
				// location.reload();
				// Need to set timeout otherwise it wont update the total

				setTimeout(function () {
					$('#cart > button').html('<span id="cart-total"><i class="fa fa-shopping-cart"></i> ' + json['total'] + '</span>');
				} , 100);


			
				
				//$('#'+key).show(); 


				$('#'+key+"productcategory").show();


				 $('#'+key+"latest").show(); 

				$('#'+key+"special").show();

				//$('#'+key+"productcategory").show(); 	

       /// for prpduct search page

				$('#'+key+"dynamic").show(); 	
				
				$("#extra").show(); 

				$('#'+key+"productsearch").show();

				$('#'+key+"featured").show();

				$('#'+key+"btncartbutton").show();	

				$('#'+key+"bestseller").show();

				$('#'+key+"search").show();

				$('#'+key+"productSpecial").show();  

				$('#'+key+"productmanufacture").show(); 

			

			

				
	

				if (getURLVar('route') == 'checkout/cart' || getURLVar('route') == 'checkout/checkout') {
					location = 'index.php?route=checkout/cart';
				} else {
					$('#cart > ul').load('index.php?route=common/cart/info ul li');
				}
				
			},
			error: function(xhr, ajaxOptions, thrownError) {
				alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
			}
		});
	}
}

var voucher = {
	'add': function() {

	},
	'remove': function(key) {
		$.ajax({
			url: 'index.php?route=checkout/cart/remove',
			type: 'post',
			data: 'key=' + key,
			dataType: 'json',
			beforeSend: function() {
				$('#cart > button').button('loading');
			},
			complete: function() {
				$('#cart > button').button('reset');
			},
			success: function(json) {
				// Need to set timeout otherwise it wont update the total
				setTimeout(function () {
					$('#cart > button').html('<span id="cart-total"><i class="fa fa-shopping-cart"></i> ' + json['total'] + '</span>');
				}, 100);

				if (getURLVar('route') == 'checkout/cart' || getURLVar('route') == 'checkout/checkout') {
					location = 'index.php?route=checkout/cart';
				} else {
					$('#cart > ul').load('index.php?route=common/cart/info ul li');
				}
			},
			error: function(xhr, ajaxOptions, thrownError) {
				alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
			}
		});
	}
}

var wishlist = {
	'add': function(product_id) {
		$.ajax({
			url: 'index.php?route=account/wishlist/add',
			type: 'post',
			data: 'product_id=' + product_id,
			dataType: 'json',
			success: function(json) {
				$('.alert-dismissible').remove();

				if (json['redirect']) {
					location = json['redirect'];
				}

				if (json['success']) {
					$('#content').parent().before('<div class="alert alert-success alert-dismissible"><i class="fa fa-check-circle"></i> ' + json['success'] + ' <button type="button" class="close" data-dismiss="alert">&times;</button></div>');
				}

				$('#wishlist-total span').html(json['total']);
				$('#wishlist-total').attr('title', json['total']);

				$('html, body').animate({ scrollTop: 0 }, 'fast');
			},
			error: function(xhr, ajaxOptions, thrownError) {
				alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
			}
		});
	},
	'remove': function() {

	}
}

			var compare = {
				'add': function(product_id) {
					$.ajax({
						url: 'index.php?route=product/compare/add',
						type: 'post',
						data: 'product_id=' + product_id,
						dataType: 'json',
						success: function(json) {
							$('.alert-dismissible').remove();

							if (json['success']) {
								$('#content').parent().before('<div class="alert alert-success alert-dismissible"><i class="fa fa-check-circle"></i> ' + json['success'] + ' <button type="button" class="close" data-dismiss="alert">&times;</button></div>');

								$('#compare-total').html(json['total']);

								$('html, body').animate({ scrollTop: 0 }, 'slow');
							}
						},
						error: function(xhr, ajaxOptions, thrownError) {
							alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
						}
					});
				},
				'remove': function() {

				}
			}

/* Agree to Terms */
$(document).delegate('.agree', 'click', function(e) {
	e.preventDefault();

	$('#modal-agree').remove();

	var element = this;

	$.ajax({
		url: $(element).attr('href'),
		type: 'get',
		dataType: 'html',
		success: function(data) {
			html  = '<div id="modal-agree" class="modal">';
			html += '  <div class="modal-dialog">';
			html += '    <div class="modal-content">';
			html += '      <div class="modal-header">';
			html += '        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>';
			html += '        <h4 class="modal-title">' + $(element).text() + '</h4>';
			html += '      </div>';
			html += '      <div class="modal-body">' + data + '</div>';
			html += '    </div>';
			html += '  </div>';
			html += '</div>';

			$('body').append(html);

			$('#modal-agree').modal('show');
		}
	});
});

// Autocomplete */
(function($) {
	$.fn.autocomplete = function(option) {
		return this.each(function() {
			this.timer = null;
			this.items = new Array();

			$.extend(this, option);

			$(this).attr('autocomplete', 'off');

			// Focus
			$(this).on('focus', function() {
				this.request();
			});

			// Blur
			$(this).on('blur', function() {
				setTimeout(function(object) {
					object.hide();
				}, 200, this);
			});

			// Keydown
			$(this).on('keydown', function(event) {
				switch(event.keyCode) {
					case 27: // escape
						this.hide();
						break;
					default:
						this.request();
						break;
				}
			});

			// Click
			this.click = function(event) {
				event.preventDefault();

				value = $(event.target).parent().attr('data-value');

				if (value && this.items[value]) {
					this.select(this.items[value]);
				}
			}

			// Show
			this.show = function() {
				var pos = $(this).position();

				$(this).siblings('ul.dropdown-menu').css({
					top: pos.top + $(this).outerHeight(),
					left: pos.left
				});

				$(this).siblings('ul.dropdown-menu').show();
			}

			// Hide
			this.hide = function() {
				$(this).siblings('ul.dropdown-menu').hide();
			}

			// Request
			this.request = function() {
				clearTimeout(this.timer);

				this.timer = setTimeout(function(object) {
					object.source($(object).val(), $.proxy(object.response, object));
				}, 200, this);
			}

			// Response
			this.response = function(json) {
				html = '';

				if (json.length) {
					for (i = 0; i < json.length; i++) {
						this.items[json[i]['value']] = json[i];
					}

					for (i = 0; i < json.length; i++) {
						if (!json[i]['category']) {
							html += '<li data-value="' + json[i]['value'] + '"><a href="#">' + json[i]['label'] + '</a></li>';
						}
					}

					// Get all the ones with a categories
					var category = new Array();

					for (i = 0; i < json.length; i++) {
						if (json[i]['category']) {
							if (!category[json[i]['category']]) {
								category[json[i]['category']] = new Array();
								category[json[i]['category']]['name'] = json[i]['category'];
								category[json[i]['category']]['item'] = new Array();
							}

							category[json[i]['category']]['item'].push(json[i]);
						}
					}

					for (i in category) {
						html += '<li class="dropdown-header">' + category[i]['name'] + '</li>';

						for (j = 0; j < category[i]['item'].length; j++) {
							html += '<li data-value="' + category[i]['item'][j]['value'] + '"><a href="#">&nbsp;&nbsp;&nbsp;' + category[i]['item'][j]['label'] + '</a></li>';
						}
					}
				}

				if (html) {
					this.show();
				} else {
					this.hide();
				}

				$(this).siblings('ul.dropdown-menu').html(html);
			}

			$(this).after('<ul class="dropdown-menu"></ul>');
			$(this).siblings('ul.dropdown-menu').delegate('a', 'click', $.proxy(this.click, this));

		});
	}
})(window.jQuery);
