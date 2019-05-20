/**
 * @author Splitit
 * @copyright 2017-2018 Splitit
 * @since 1.6.0
 * @license BSD 2 License
 */
 
var isClicked = false;

function login(url){

    var api_key = $('#api_key').val();
    var api_user_name = $('#api_user_name').val();
    var api_password = $('#api_password').val();

    if(api_key == ''){
        alert('Please enter api key');
        return false;
    }else if(api_user_name == ''){
        alert('Please enter api user name');
        return false;
    }else if(api_password == ''){
        alert('Please enter api password');
        api_password;
        return false;
    }

    if(isClicked) {
        return;
    }

    isClicked = true;    
    $('#splitit_button').find('span').text('Checking...');
	
    $.post(url+"modules/splitit/ajax.php", {action:"login", reqInit: 'false', 'api_key' : api_key, 'api_user_name' : api_user_name, 'api_password' : api_password }, function(data){
        
        $('#splitit_button').find('span').text('Check Settings');
        isClicked = false;

        alert(data.message);

	},'json')
}

// SplitIt Admin Javascript Start Here
$(document).ready(function() {

	// Allowed Countries
    if($('#allowed_countries').val() == 0){
        $('#specific_countries').attr('disabled', false).removeClass('blur');
    }else{
        $('#specific_countries').attr('disabled', true).addClass('blur');
    }

    $('#allowed_countries'). on('change', function() {
        specific = $(this).val();
        //console.log(specific);
        if(specific == 0){
            $('#specific_countries').attr('disabled', false).removeClass('blur');

        }else{
            $('#specific_countries').attr('disabled', true).addClass('blur');
        }
    }); 

    // Show and Hide Installment Price Setup
    if($('#enable_price').val() == 0){
    	$('.instalment_price_row').hide();
    }else{
    	$('.instalment_price_row').show(500);
    }

    $('#enable_price'). on('change', function() {
        if($(this).val() == 0){
            $('.instalment_price_row').hide();

        }else{
            $('.instalment_price_row').show(500);
        }
    });

    // Check first payment Percentage
    $('#splitit_submit').on('click', function(e){

	    if($('#percentage_of_order').val() > 50){
	    	alert('Percentage should be less than or equal to 50%');
	    	return false;
	    }else{
	    	return true;
	    }


    });

    // First Payment

    if($('#first_payment').val() == 'percentage'){
		$('.percentage_of_order').show(500);
    }else{
		$('.percentage_of_order').hide();
    }

    $('#first_payment').on('change', function(){

    	if($(this).val() == 'percentage'){
    		$('.percentage_of_order').show(500);
    	}else{
    		$('.percentage_of_order').hide();
    	}

    });


});

