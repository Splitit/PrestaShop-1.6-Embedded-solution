/**
 * @author Splitit
 * @copyright 2017-2018 Splitit
 * @since 1.6.0
 * @license BSD 2 License
 */
 
var isLogedIn = 0;
var isFormFieldsClicked = 0;
var isClicked = false;

function popWin(url,win,para) {
    var win = window.open(url,win,para);
    win.focus();
}

function login(baseUrl){
    $("#overlay").show();
    $.post(baseUrl+"modules/splitit/ajax.php", {action:"login"}, function(res){

        $("#overlay").hide();

        if(res.status == true && res.code == 1){

            isFormFieldsClicked = 1;            
            isLogedIn = 1;
            console.log(res.message);
        }else if(res.status == false && res.code == 4){
            if(res.redirect != ''){
                window.location.href = res.redirect;
            }
            
        }else{
            isLogedIn = 0;
            alert(res.message);
        }

    },'json')
}

function getInstallmentPlans(baseUrl){

    if(isClicked) {
        return;
    }

    // If session not created create new session.
    if(isLogedIn == 0){
        login(baseUrl);
    }

    if($('#splitit_installments_no').val() == ''){
        showSplititError('Please select number of installment');
        /*alert('Please select installment');*/
        return false;
    }

    isClicked = true;
    hideSplititError();
    $.ajax({
        url : baseUrl+"modules/splitit/ajax.php",
        type : 'POST',
        dataType:'json',
        data: { "action": "installmentPlans" , "selectedInstallment" : $('#splitit_installments_no').val() },
        beforeSend: function(){
            $("#overlay").show();
            //$('.terms-condition-loader').show();
        },
        complete: function(){
            $("#overlay").hide();
            //$('.terms-condition-loader').hide();
        },
        success : function(res){
            
            isClicked = false;          
            if (res.status == true && res.code == 1) {              
                $('body').append(res.message);
            }else if(res.status == false && res.code == 4){
                if(res.redirect != ''){
                    window.location.href = res.redirect;
                }
                
            } else {
                showSplititError(res.message);
                //alert(res.message);                
            }

        },
        error: function(res){
            isClicked = false;
            //alert(res);
            showSplititError(res);
        }
    });

}

function confirmPayment(baseUrl){

    if(isClicked) {
        return;
    }   

    // If session not created create new session.
    if(isLogedIn == 0){
        login(baseUrl);
    }

    // Validate required form fields
    if(!validateFormFields())
        return false;

    // Remove whitespace from credit card number
    /*$('#splitit_cc_number').val($('#splitit_cc_number').val().replace(/\s/g, ''));*/

    isClicked = true;
    hideSplititError();
    $.ajax({
        //url : baseUrl+"modules/splitit/validation",
        url : baseUrl+"modules/splitit/ajax.php",
        type : 'POST',
        dataType:'json',
        data: $('#splitit_form').serialize() + "&action=confirm",
        beforeSend: function(){
            $("#overlay").show();
            //$('.terms-condition-loader').show();
        },
        complete: function(){
            $("#overlay").hide();
            //$('.terms-condition-loader').hide();
        },
        success : function(res){

            isClicked = false;
            console.log(res.message);

            if ((res.status == true && res.code == 1) || (res.status == false && res.code == 4)) {
                
                //alert(res.message);
                console.log(res.message);
                if(res.redirect != ''){
                    window.location.href = res.redirect;
                }

            } else if(res.status == false && res.code == 2) {
                showSplititError(res.message);
                //alert(res.message);
                
            } else if(res.status == false && res.code == 3){
                //$('#splitit_form .error').remove();
                var errors = '';
                $.each(res.errors, function(key, value){

                    if($('#splitit_'+key).val() == '' || $('#splitit_'+key).attr('name') == 'terms'){
                        errors += '<p class="error_'+key+'">'+value+'</p>';

                        //$('#splitit_'+key).parent('div').append('<div class="error error_'+key+'">'+value+'</span>');
                    }/*else{
                        $('#splitit_form .error_'+key).remove();
                    }*/

                });

                if(errors != ''){
                    showSplititError(errors);
                }else{
                    hideSplititError();
                }

            }

        },
        error: function(res){
            isClicked = false;
            //alert(res);
            showSplititError(res);
        }        

    });


}

function validateFormFields(){

    if($('#splitit_cc_number').val() == ''){
        showSplititError('Please enter credit card number');
        return false;
    }else if($('#splitit_expiration_month').val() == ''){
        showSplititError('Please select expiration month');
        return false;
    }else if($('#splitit_expiration_yr').val() == ''){
        showSplititError('Please select expiration year');
        return false;
    }else if($('#splitit_cc_cid').val() == ''){
        showSplititError('Please enter CVV Number');
        return false;
    }else if($('#splitit_installments_no').val() == ''){
        showSplititError('Please select number of installment');
        return false;
    }else if(!$('#splitit_terms').is(':checked')){
        showSplititError('Please agree to terms and conditions');
        return false;
    }else{
        hideSplititError();
    }

    return true;

}

function showSplititError(msg){

    $('.splitit-payment-errors').html(msg).fadeIn(1000);
}

function hideSplititError(){

    $('.splitit-payment-errors').html('').fadeOut(100);
}

// close Approval popup
function closeApprovalPopup(){
    jQuery("#approval-popup, .approval-popup_ovelay").hide();
}

jQuery(document).on("click", "#payment-schedule-link", function(){
    jQuery("#approval-popup").addClass("overflowHidden");
    jQuery('#payment-schedule, ._popup_overlay').show();
});
jQuery(document).on("click", "#complete-payment-schedule-close", function(){
    jQuery("#approval-popup").removeClass("overflowHidden");
    jQuery('#payment-schedule, ._popup_overlay').hide();    
});



/*function login(url){
    $.post(url+"modules/splitit/ajax.php", {action:"login"}, function(data){

        if(data.success == true){
            console.log('Logged in succfully!');
        }else{
            console.log('Login failure');
        }

    },'json')
}*/

