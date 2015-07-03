//var question = "What do you think about " + sa_products[] + " ?";
// <div class=\"sa_ratingBox\"> 
//                     How would you rate your overall shopping experience so far? 
//                     <div id=\"Overall_stars\"> 
//                         <span id=\"Overall1\" class=\"sa_star\"></span> 
//                         <span id=\"Overall2\" class=\"sa_star\"></span> 
//                         <span id=\"Overall3\" class=\"sa_star\"></span> 
//                         <span id=\"Overall4\" class=\"sa_star\"></span> 
//                         <span id=\"Overall5\" class=\"sa_star\"></span> 
//                         <input class=\"sa_value\" type=\"hidden\" id=\"Overall\" /> 
//                     </div> 
//                 </div> 

sa_id = '4624';
sa_contents = ' <div id=\"shopper_approved\" class=\"sa_start_survey\"> <div id=\"sa_outer\"> <div id=\"sa_rounded\"> <div id=\"sa_header\"> <img id=\"sa_close\" class=\"sa_close\" src=\"https://www.shopperapproved.com/thankyou/images/xbutton.gif\" /> <img id=\"sa_header_img\" src=\"https://www.shopperapproved.com/thankyou/custom/4624.png\" /> <span id=\"sa_header_text\"> Thank You for Your Order </span> </div> <div id=\"sa_ratings\"> <!-- This is an optional section underneath the Thank You. It needs a div. --> <div class=\"sa_ratingBox\"> How would you rate your overall shopping experience so far? <div id=\"Overall_stars\"> <span id=\"Overall1\" class=\"sa_star\"></span> <span id=\"Overall2\" class=\"sa_star\"></span> <span id=\"Overall3\" class=\"sa_star\"></span> <span id=\"Overall4\" class=\"sa_star\"></span> <span id=\"Overall5\" class=\"sa_star\"></span> <input class=\"sa_value\" type=\"hidden\" id=\"Overall\" /> </div> </div> <div class=\"sa_ratingBox\"> How likely are you to recommend our site to others? <div id=\"Recommend_stars\"> <span id=\"Recommend1\" class=\"sa_star\"></span> <span id=\"Recommend2\" class=\"sa_star\"></span> <span id=\"Recommend3\" class=\"sa_star\"></span> <span id=\"Recommend4\" class=\"sa_star\"></span> <span id=\"Recommend5\" class=\"sa_star\"></span> <input class=\"sa_value\" type=\"hidden\" id=\"Recommend\" /> </div> </div> <div class=\"sa_ratingBox\"> How likely are you to buy from us again if you ever need a similar product/service? <div id=\"Rebuy_stars\"> <span id=\"Rebuy1\" class=\"sa_star\"></span> <span id=\"Rebuy2\" class=\"sa_star\"></span> <span id=\"Rebuy3\" class=\"sa_star\"></span> <span id=\"Rebuy4\" class=\"sa_star\"></span> <span id=\"Rebuy5\" class=\"sa_star\"></span> <input class=\"sa_value\" type=\"hidden\" id=\"Rebuy\" /> </div> </div> <div id=\"sa_lowrating\"> It appears from your ratings that you may have an issue that needs attention. If you left a 1 or 2-Star rating by mistake, please correct it before continuing. Or, if your rating is correct, please leave us some feedback so that we can better serve you. <br/> <br/> <a id=\"sa_continue_feedback\">Continue to feedback.</a><br/><br/> <a id=\"stop_rating\" class=\"sa_close\">No thanks. I would rather not participate in rating.</a> </div> <div id=\"sa_comments\" class=\"sa_ratings\"> <div id=\"sa_comment_descriptions\">Please type a quick message about your shopping experience so far.</div> <textarea id=\"comments\" class=\"sa_comments\" cols=\"30\" rows=\"3\" onfocus=\"if (this.value == \'Type your message here...\') { this.value = \'\'; this.style.color = \'black\'; }\">Type your message here...</textarea> <div class=\"sa_message\" id=\"sa_comment_message\"></div> <br/> <br/> <div id=\"sa_tooptin\"> Should we follow up to make sure you\'re satisfied with your order? <br/> <label> <input name=\"sa_followup\" value=\"yes\" type=\"radio\"> Yes</label> &nbsp;&nbsp;&nbsp; <label> <input name=\"sa_followup\" value=\"no\" type=\"radio\"> No</label> </div> </div> <div id=\"sa_last_chance\">Are you sure? There are several benefits to our free follow up service, including order confirmation, additional rating/review options, and customer care resolution services should any issues arise.</div> <div id=\"sa_optin\"> <div id=\"sa_pleaseenter\"> Please enter your information below so we can confirm that you received your order and that the transaction went smoothly. <br /> <div class=\"sa_optin\">Name<input id=\"sa_name\" type=\"text\" /></div> <div id=\"sa_emailbox\" class=\"sa_optin\">Email<input id=\"sa_email\" type=\"text\" /></div> <div class=\"sa_message\" id=\"sa_email_message\"></div> </div> <p id=\"sa_safeemail\" style=\"clear: both; padding-top: 10px;\"> <img src=\"https://www.shopperapproved.com/thankyou/images/minicheckmark.jpg\" /> <span id=\"sa_safe\">Your email is safe.</span> It will <u>only</u> be used for order confirmation and customer care. </p> <input id=\"shopper_submit\" type=\"button\" value=\"Submit\" /> </div> <input id=\"shopper_submit_second\" type=\"button\" value=\"Submit\" style=\"display: none;\" /> <img id=\"sa_footer_img\" src=\"https://www.shopperapproved.com/thankyou/images/just-powered.png\" alt=\"Survey Powered by Shopper Approved\" /> <div style=\"clear: both;\"></div> </div> <div id=\"sa_thankyou\" style=\"display: none;\"> Thank you for your feedback. As requested, you will receive a follow up survey email with additional rating, review and customer care options. </div> <div id=\"sa_thankyou_no_email\" style=\"display: none;\"> Thank you for your feedback. </div> </div> </div> </div> <div id=\"shopper_background\" class=\"sa_start_survey\"></div> ';
sa_cache = false;
sa_css = '#shopper_approved { z-index: 2147483647; font: 14px arial,sans-serif !important; color: black !important; display: none; position: absolute; width: 100%; } #shopper_approved img { width: inherit !important; height: inherit !important; } #shopper_background { position: fixed !important; left: 0 !important; top: 0 !important; width: 100% !important; height: 100% !important; z-index: 32765 !important; background-color:#333333 !important; display: none; opacity: 0.40 !important; filter: alpha(opacity=40) !important; } #shopper_approved #sa_outer { margin: 10px auto; max-width: 480px !important; padding: 0 !important; } #shopper_approved #sa_rounded { background-color: white !important; padding: 0 0 20px 0 !important; border: 1px solid #ccc !important; -moz-box-shadow: 2px 2px 5px #888 !important; -webkit-box-shadow: 2px 2px 5px #888 !important; box-shadow: 2px 2px 5px #ccc !important; -moz-border-radius: 10px !important; -webkit-border-radius: 10px !important; -khtml-border-radius: 10px !important; border-radius: 10px !important; } #shopper_approved #sa_rounded * { position: relative; } #shopper_approved #sa_header { border:0 !important; text-align: left !important; padding: 20px 0 0 30px !important; vertical-align: middle !important; font-style: italic !important; font-size: 24px !important; position: relative; } #shopper_approved #sa_close { border: 0 !important; display: block !important; float: right !important; margin: -10px 10px 0 0 !important; cursor: pointer; width: 12px !important; height: 12px !important; } #shopper_approved #sa_ratings { text-align: left !important; padding: 0 30px !important; position: relative; } #shopper_approved #sa_thankyou { text-align: left !important; padding: 0 30px !important; position: relative; } #shopper_approved #sa_thankyou_no_email { text-align: center !important; padding: 0 30px !important; position: relative; } #shopper_approved .sa_ratingBox { padding: 20px 0 0 0 !important; font: 14px arial,sans-serif !important; color: black !important; } #shopper_approved select.sa_value { width: 200px !important; margin-top: 7px !important; } .sa_star { cursor:pointer; background: url(\"https://www.shopperapproved.com/thankyou/simplestar.png\") no-repeat !important; width: 24px !important; height: 24px !important; display: inline-block; } .sa_activestar { background-position: 0 -24px !important; } #shopper_approved select,#shopper_approved input,#shopper_approved textarea { font-size: 16px !important; } #sa_comments { padding: 20px 0 !important; display: none; font: 16px arial,sans-serif !important; } #sa_lowrating { padding: 20px 0 !important; display: none; font: 16px arial,sans-serif !important; color: red !important; } #sa_lowrating a { font: 16px arial,sans-serif !important; color: blue !important; cursor: pointer; } #shopper_approved textarea { border: 1px solid #bbb !important; color:gray; padding:2px; width: 100% !important; } .sa_heading { border: 1px solid #bbb !important; color:gray; padding:2px; width: 100% !important; margin-bottom: 20px; } #sa_optin { padding: 0 !important; display: none; font: 16px arial,sans-serif !important; color: black !important; } #sa_optin input[type=\"text\"] { border: 1px solid #bbb !important; width: 300px !important; color: black !important; float: right; margin-right: 50px; } #shopper_approved input[type=\"radio\"] { float: none !important; opacity: 1 !important; } #sa_optin .sa_optin { padding: 10px 0 !important; } #sa_last_chance { display: none; font-size: smaller !important; color: red !important; padding: 0 0 6px 0 !important; } #shopper_submit,#disabled_submit { border: inherit !important; padding: inherit !important; background-color: transparent !important; text-indent: -999999px; color: transparent !important; background-image: url(https://www.shopperapproved.com/thankyou/images/submit-feedback.png) !important; width:175px !important; height:40px !important; } #shopper_approved #sa_footer_img { float: right !important; display: block !important; } #sa_safeemail { } #sa_safe { font-weight: bold; } .sa_message { color: red; } #sa_header_img { max-width: 300px; display: inline-block; margin-bottom: 15px; } #sa_header_text { display: inline-block; } @media (max-width: 600px) { #sa_optin input[type=\"text\"] { margin-right: 0 !important; } } @media (max-width: 540px) { #sa_optin input[type=\"text\"] { margin-right: 0 !important; width: 200px !important; } } @media (max-width: 500px) { #shopper_approved #sa_outer { margin: 0 !important; padding: 10px !important; } } @media (max-width: 450px) { #sa_header_img { max-width: 200px; } }  #shopper_approved #sa_header { font-style: normal !important; font-weight: bold; } #shopper_approved #sa_outer { max-width: 580px !important; } #sa_feature { font-size: 11px; visibility: hidden; } .product_desc { font-size: 16px; } #ProductComments { font: 16px arial,sans-serif !important; } #sa_optin input[type=\"text\"] { width: 360px !important; } @media (max-width: 450px) { #sa_optin input[type=\"text\"] { display: block !important; width: 100% !important; float: none; margin-bottom: 10px; } } ';

String.prototype.trim = function() {
    return this.replace(/^\s+|\s+\$/g, '');
}

if (!Object.keys) {
  Object.keys = function(obj) {
    var keys = [];

    for (var i in obj) {
      if (obj.hasOwnProperty(i)) {
        keys.push(i);
      }
    }

    return keys;
  };
}

var sa_warnings = {
'rating':'Please give us a rating.',
'watermark':'Type your message here...',
'email':'Please enter your email for followup.',
'emailname':'Please enter your email and name for followup.',
'invalidemail':'Please enter a valid email address.',
'invalidname':'Please don\'t put an email in the name field.',
'comment':'Please leave us a quick comment about your overall experience.',
'links':'Links are not allowed.'};

sa_setCookie = function(c_name,value,exdays)
{
    var exdate=new Date();
    exdate.setDate(exdate.getDate() + exdays);
    var c_value=escape(value) + ((exdays==null) ? "" : "; expires="+exdate.toUTCString());
    document.cookie=c_name + "=" + c_value;
}

sa_getCookie = function(c_name)
{
    var i,x,y,ARRcookies=document.cookie.split(";");
    for (i=0;i<ARRcookies.length;i++)
    {
        x=ARRcookies[i].substr(0,ARRcookies[i].indexOf("="));
        y=ARRcookies[i].substr(ARRcookies[i].indexOf("=")+1);
        x=x.replace(/^\s+|\s+$/g,"");
        if (x==c_name)
        {
            return unescape(y);
        }
    }
    return null;
} 

//Rewritten for TagMan
function saLoadScript(src) { 
    var js = window.document.createElement("script"); 
    js.src = src; 
    js.type = "text/javascript"; 
    document.getElementsByTagName("head")[0].appendChild(js); 
}

/*
var _gaq = _gaq || [];
_gaq.push(['shopperTracker._setAccount', 'UA-39194249-1']);

(function() {
var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
})();
*/

function sa_track_event(action, label, value)
{
    /*if (typeof(label) === 'undefined') label = '';
    if (typeof(value) === 'undefined') value = '';
    _gaq.push(['shopperTracker._trackEvent', action, label, value]);
    */
}

sa_track_event('Thank You Displayed', 'siteid', sa_values['site']);


//http://weblogs.asp.net/joelvarty/archive/2009/05/07/load-jquery-dynamically.aspx
var jQueryScriptOutputted = false;
var sa_jqTries = 0;
var sa_actiontaken = false;
function initsaJQ() {      
    //if the jQuery object isn't available
    if (typeof(saJQ) == 'undefined') {
        if (! jQueryScriptOutputted) {
            //only output the script once..
            jQueryScriptOutputted = true;

            //output the script (load it from google api)
            saLoadScript("//shopperapproved.com/page/js/jquery.noconflict.js");
        }
        sa_jqTries++;
        if (sa_jqTries > 200 && typeof(sa_troubleshoot) != 'undefined' && sa_troubleshoot == 3) {
            startTicket('There was a problem loading JQuery.');
        }
        setTimeout("initsaJQ()", 50);
    } else {
        
        if (typeof(sa_troubleshoot) != 'undefined' && sa_troubleshoot == 3) {
            writeTroubleShoot('JQuery loaded.', 'green');       
        }
        saJQ(function() {
            
            if (typeof(saJQloaded) == 'function') {
                saJQloaded();
            }
            
            if (typeof(sa_values['emailAll']) != 'undefined' && sa_values['emailAll']) {
                sa_values['version'] = 'skip initial';
                sa_values['rnd'] = Math.random();
                if (typeof(sa_products) != 'undefined') {
                    sa_values['products'] = sa_products;
                }
                var url = (typeof(sa_submit_page) == 'undefined')
                    ? 'https://www.shopperapproved.com/thankyou/initial.php'
                    : sa_submit_page;
                url += '?' + saJQ.param(sa_values); 
                saLoadScript(url);
                sa_values['version'] = '';
            }
            
            for (var i in sa_values) {
                saJQ('#' + i + '_stars').children('.sa_star').slice(0, sa_values[i]).toggleClass('sa_activestar', true);
                saJQ('#' + i + '_stars').children('.sa_value').val(sa_values[i]);
            }    
                  
            //Start the Survey
            if (sa_values['name'])
                saJQ('#sa_name').val(sa_values['name']);
            if (sa_values['email'])
                saJQ('#sa_email').val(sa_values['email']);
                
            var y = saJQ(window).scrollTop();
            saJQ('#shopper_approved').css('top', y);
            
            if ((typeof(sa_hold) == 'undefined' || !sa_hold) && (!sa_values['hold'])) {
                StartShopperApproved();
            }
            
            saJQ('#shopper_approved .sa_close').click( function () {
                saJQ('.sa_start_survey').fadeOut(300);
                sa_track_event('Closed'); 
                
                sa_open_gts(); 
            });
            
            if (!!saJQ.fn.on) {
                saJQ('.sa_star').on('click touchend MSPointerUp', function () {
                    sa_actiontaken = true; 
                    var num = GetNum(this);
                    sa_values[saJQ(this).siblings('.sa_value')[0]['id']] = num;
                    
                    saJQ(this).siblings('.sa_value').val(num);
                    AllDone();
                });    
            }
            else {
                saJQ('.sa_star').click(function () {
                    sa_actiontaken = true; 
                    var num = GetNum(this);
                    sa_values[saJQ(this).siblings('.sa_value')[0]['id']] = num;
                    
                    saJQ(this).siblings('.sa_value').val(num);
                    AllDone();
                });    
            }
            
            saJQ('#ProductComments').focus( function () {
                saJQ('#sa_feature').css('visibility', 'visible');    
            }).keyup( function () {
                var chars = saJQ(this).val().length;
                if (chars >= 50) {
                    saJQ('#sa_feature').html('Thanks for your feedback!');    
                }  
                else {
                    saJQ('#sa_countdown').html(50-chars);
                } 
            });
            
            
            
            saJQ('.sa_star').mouseover( function () {
                saJQ(this).siblings('.sa_star').toggleClass('sa_activestar', false);
                saJQ(this).prevAll('.sa_star').andSelf().toggleClass('sa_activestar', true);
            });
            saJQ('.sa_star').mouseout( function () {
                var num = saJQ(this).siblings('.sa_value').val();
                saJQ(this).parent().children('.sa_star').toggleClass('sa_activestar', false);
                if (num != '')
                    saJQ(this).parent().children('.sa_star').slice(0, num).toggleClass('sa_activestar', true);   
            });
            
            saJQ('select').change( function() {
                sa_values[saJQ(this).prop('id')] = saJQ(this).val();
                AllDone();  
            });
            
            saJQ('#sa_continue_feedback').click( function () {
                saJQ('#sa_comments').slideDown(300, function () { ScrollPage(this) } ); 
                sa_values['comments_mandatory'] = true;    
            });
            
            saJQ('#shopper_approved input[name="sa_followup"]').click( function () {
                sa_values['email_mandatory'] = (saJQ(this).val() == 'yes');
                if (saJQ(this).val() == 'no') {
                    
                    if (saJQ('#sa_last_chance').html() == '&nbsp;') {
                        saJQ('#sa_pleaseenter').hide();
                        saJQ('#sa_emailbox').hide();   
                        saJQ('#sa_safeemail').hide();   
                    }
                    else {
                        saJQ('#sa_last_chance').show();
                    }
                    saJQ('#sa_email').val('');
                }
                else {
                    
                    if (saJQ('#sa_last_chance').html() == '&nbsp;') {
                        saJQ('#sa_pleaseenter').show();
                        saJQ('#sa_emailbox').show(); 
                        saJQ('#sa_safeemail').show();   
                    }
                    
                    saJQ('#sa_last_chance').hide();
                    saJQ('#sa_email').val(sa_values['email']);
                }
                    
                if (sa_values['optin'] != 'silent' && sa_values['optin'] != 'submit') {
                    saJQ('#sa_optin').slideDown(300, function () { ScrollPage(this) } );
                }     
            });
            
            saJQ('#shopper_submit, #shopper_submit_second').click( function () {
                
                saJQ('.sa_watermark').each( function () {
                   saJQ(this).val('').css('color', 'black').toggleClass('sa_watermark', false); 
                });
                
                
                sa_values['comments'] = '';
                saJQ('.sa_comments,.sa_info,.sa_heading').each ( function () {
                    if (saJQ(this).val().trim())
                        sa_values[saJQ(this).attr('id')] = saJQ(this).val().trim();    
                });
                
                if ( (sa_values['optin'] != 'silent' && sa_values['optin'] != 'submit') || sa_values['email_mandatory']) {
                    sa_values['name'] = saJQ('#sa_name').val();
                    sa_values['email'] = saJQ('#sa_email').val();
                    var reg = /^[^@]+@[^@.]+\.[^@]*\w\w$/  ;
                } 
                var commentreg = /<\s*a\s+[^>]+>/;
                
                saJQ('#sa_email_message').html('');
                saJQ('#sa_comment_message').html('');
                var sa_valid = true; 
                
                sa_values['comments'] = sa_values['comments'].replace(sa_warnings['watermark'], '');   
                sa_values['comments'] = sa_values['comments'].replace(/^\s+|\s+$/g, "");
                
                if (typeof(sa_values['ProductRating']) == 'undefined' 
                    &&
                    (typeof(sa_values['Overall']) == 'undefined' || sa_values['Overall'] == null || sa_values['Overall'] == 0) ) {
                    saJQ('#sa_rating_message').html(sa_warnings['rating']); 
                    sa_valid = false;     
                }
                
                if (sa_values['all_mandatory'] && (sa_values['name'].trim() == '' || sa_values['email'].trim() == '')) {
                    saJQ('#sa_email_message').html(sa_warnings['emailname']); 
                    sa_valid = false;   
                }
                else if (sa_values['optin'] != 'silent' && sa_values['optin'] != 'submit' && sa_values['email_mandatory'] && !sa_values['email']) {
                    saJQ('#sa_email_message').html(sa_warnings['email']); 
                    sa_valid = false;   
                }
                else if (sa_values['optin'] != 'silent' && sa_values['optin'] != 'submit' && sa_values['email'] && !reg.test(sa_values['email'])) {
                    saJQ('#sa_email_message').html(sa_warnings['invalidemail']);
                    sa_valid = false; 
                }
                else if (sa_values['name'].indexOf('@') >= 0) {
                    saJQ('#sa_email_message').html(sa_warnings['invalidname']);
                    sa_valid = false; 
                }
                
                if (sa_values['all_mandatory']) {
                    sa_values['forcecomments'] = true;    
                }
                
                if (typeof(sa_values['forcecomments']) != 'undefined' && sa_values['forcecomments'] && sa_values['comments'] == '') {
                    saJQ('#sa_comment_message').html(sa_warnings['comment']); 
                    sa_valid = false;
                    ScrollPage('#sa_comments');    
                }
                else if (typeof(sa_values['forcecomment']) != 'undefined' && sa_values['forcecomment'] && sa_values['comments'] == '') {
                    saJQ('#sa_comment_message').html(sa_warnings['comment']);   
                    sa_valid = false;  
                    ScrollPage('#sa_comments');  
                }
                else if (commentreg.test(sa_values['comments'])) {
                    saJQ('#sa_comment_message').html(sa_warnings['links']);
                    sa_valid = false; 
                    ScrollPage('#sa_comments');  
                }
                else if (typeof(sa_product_desc) != 'undefined' && saJQ('#ProductComments').val() == '') {
                    saJQ('#sa_comment_message').html(sa_warnings['comment']); 
                    sa_valid = false;
                    ScrollPage('#ProductComments');    
                }
                
                
                if (sa_valid) { 
                    var validdata = true;
                    if (typeof(sa_troubleshoot) != 'undefined' && sa_troubleshoot == 4) {
                        validdata = checkData();
                    }
                    
                    if (validdata) {                           
                        var sa_submit = window.document.createElement('script');
                        
                        if (typeof(sa_values['rand']) == 'undefined' || sa_values['rand'] == null)
                        {                            
                            sa_values['rand'] = Math.floor(Math.random()*1000);
                            
                            if (typeof(sa_products) == 'undefined') {
                                                    
                                var from_sa_cookie = sa_getCookie('sa_products_'+sa_values['site']);
                                if (from_sa_cookie != null && typeof(JSON) != 'undefined') {
                                    sa_values['products'] = JSON.parse(from_sa_cookie);
                                }
                                var from_sa_cookie = sa_getCookie('sa_productdetails_'+sa_values['site']);
                                if (from_sa_cookie != null && typeof(JSON) != 'undefined') {
                                    sa_values['productdetails'] = JSON.parse(from_sa_cookie);
                                }
                            }
                            else {
                                var temp_products = {};
                                var temp_productdetails = {};
                        
                                for (productid in sa_products) {
                                    
                                    if (typeof(sa_products[productid]) == 'string' || typeof(sa_products[productid]) == 'number') {
                                        sa_values['products'] = sa_products;
                                        break;  
                                    }
                                    
                                    temp_products[productid] = sa_products[productid][0];                                          
                                    temp_productdetails[productid] 
                                        = {'url' : sa_products[productid][1],
                                            'image' : sa_products[productid][2]};    
                                }
                                if (typeof(sa_values['products']) == 'undefined' && Object.keys(temp_products).length > 0) {
                                    sa_values['products'] = temp_products;
                                    sa_values['productdetails'] = temp_productdetails;
                                }
                            }
                            
                            if (typeof(sa_submit_page) == 'undefined')
                                sa_submit_page = 'https://www.shopperapproved.com/thankyou/initial.php';
                            sa_submit.src = sa_submit_page + '?' + saJQ.param(sa_values); 
                            sa_submit.type = 'text/javascript'; 
                            document.getElementsByTagName("head")[0].appendChild(sa_submit);
                            ScrollPage('#shopper_approved');
                            
                            if (typeof(gts) != 'undefined' && typeof(sa_product_desc) == 'undefined') {
                                
                                saJQ('.sa_start_survey').fadeOut(300, function () {
                                    sa_open_gts(); 
                                });                                    
                            }
                            else if (typeof(sa_noconfirmation) != 'undefined' && sa_noconfirmation) {
                                saJQ('.sa_start_survey').fadeOut(300);    
                            }
                            else {
                                saJQ('#sa_ratings').slideUp(300, function () {
                                    if (sa_values['email'])
                                        saJQ('#sa_thankyou').slideDown(300);
                                    else
                                        saJQ('#sa_thankyou_no_email').slideDown(300);  
                                });    
                            }
                            
                            
                            
                            
                            if (typeof(ShopperApprovedCompleted) == 'function') {
                                delete sa_values['email'];
                                ShopperApprovedCompleted(sa_values);
                            }
                        }
                    }
                }    
            });
            
        });
    }
    
}

function AllDone()
{
    var has_low = false;
    var has_empty = false;
    saJQ('.sa_value').each( function () {
        if (saJQ(this).val() == '')
            has_empty = true;
        else if (saJQ(this).val() < 3)
            has_low = true;    
    });
    
    if (!has_empty) {
        if (has_low) {
            saJQ('#sa_lowrating').slideDown(300, function () {  } );                        
        }
        else {
            saJQ('#sa_lowrating').hide();
            
            if (sa_values['emailed'] && sa_values['foreign']) {
                saJQ('#followup_question').hide();
                saJQ('#sa_comments').slideDown(300, function () {
                    var commentbox = this; 
                    saJQ('#sa_optin').slideDown(300, function () { ScrollPage(commentbox) } ); 
                } );    
            }            
            else {
                if ((typeof(sa_values['optin']) != 'undefined' && sa_values['optin'] == 'submit' && sa_values['email'])
                 || (typeof(sa_values['emailAll']) != 'undefined' && sa_values['emailAll'] && sa_values['email'])) {
                    saJQ('#sa_pleaseenter').hide();
                    saJQ('#sa_tooptin').hide();
                    saJQ('#sa_safeemail').hide();
                    saJQ('#sa_optin').show();
                }
                saJQ('#sa_comments').slideDown(300, function () { ScrollPage(this) } );
            }    
        }
    }
}

function GetNum(elem)
{
    return saJQ(elem).attr('id').replace(/[^0-9]+/, '');
}

function ScrollPage(next)
{
    var y = saJQ(next).offset().top;
    saJQ('html, body').animate({
        scrollTop: y
    }, 200);
}

function StartShopperApproved()
{
    if (typeof(sa_troubleshoot) != 'undefined' && sa_troubleshoot == 3) {
        writeTroubleShoot('Step 4 - Creating survey.');       
    }
    
    if (typeof(sa_onepage) != 'undefined' && sa_onepage) {
        saJQ('#sa_tooptin').hide();
        saJQ('#sa_optin').show();
        saJQ('#sa_comments').show();         
    }
    
    var y = saJQ(window).scrollTop();
    saJQ('#shopper_approved').css('top', y);
    
    saJQ('.sa_start_survey').fadeIn(300, function() {
        sa_track_event('Survey Displayed', 'siteid', sa_values['site']);
        setTimeout('AllDone()', 1000);
        if (typeof(sa_troubleshoot) != 'undefined' && sa_troubleshoot == 3) {
            setTimeout('markCreated()', 500);     
        }        
    });
}

var sarated = false;
var name = 'rated'+sa_id+'=';
var ca = document.cookie.split(';');
for(var i=0; i<ca.length; i++) {
    var c = ca[i].trim();
    if (c.indexOf(name)==0) {
        sarated = true;
        break;
    }
} 

function sa_load_div(div) {
    
    if (div == null) {
        if (document.body != null) {
            div = document.createElement('div');
            div.id = 'outer_shopper_approved';
            document.body.appendChild(div);
            sa_load_div(div);    
        }
        else {
            setTimeout('sa_load_div(null)', 500);
        }    
    }
    else {    
        div.innerHTML = sa_contents;
        initsaJQ();
    }    
}

function saAddCss(csstext) {
    var sastyle = document.createElement('style');
    sastyle.type = 'text/css';
    if (typeof(sastyle.styleSheet) != 'undefined' && typeof(sastyle.styleSheet.cssText) != 'undefined') {
        sastyle.styleSheet.cssText = csstext;   
    }
    else {
        sastyle.innerHTML = csstext;    
    }
    document.getElementsByTagName('head')[0].appendChild(sastyle);
}

if (true || !sarated)
{
    if (sa_values['site'] == 11758 || sa_values['gts']) {  //Add the code to hide GTS
        sa_css += ' #gts-s-w.gtss-ab,#gts-g-w.gtss-ab { display: none !important; } #gts-bgvignette.gtss-ab { display: none !important; } ';   
    }
    
    saAddCss(sa_css);
    
    var d = new Date();
    d.setTime(d.getTime()+(20*24*60*60*1000));
    document.cookie = 'rated'+sa_id+'=true; expires='+d.toGMTString();
    var div = document.getElementById('outer_shopper_approved');   
    
    sa_load_div(div);
}

function sa_open_gts() {
    
    //If gts-fs-main exists then we hid it with the loaded css. Show it now.
    //If it doesn't, at least the GTS popup would have appeared before now.
    if (sa_values['site'] == 11758 || sa_values['gts']) {   
        sa_css = ' #gts-s-w.gtss-ab,#gts-g-w.gtss-ab { display: block !important; } #gts-bgvignette.gtss-ab { display: block !important; } '; 
        saAddCss(sa_css);
    }
    else {  //I have to leave this here for some unfortunate backward compatibility.   
        var gtsbox = saJQ('#gts-fs-main');
        if (saJQ(gtsbox).length == 0 && typeof(gts) != 'undefined') { //They commented something out.
            for (var i=0; i<gts.length; i++) {
                if (gts[i][0] == 'id') {
                    if (typeof(holdgts) != 'undefined' && holdgts) {
                        var s = document.getElementsByTagName("script")[0];
                        s.parentNode.insertBefore(holdgts, s);    
                    }
                    else {
                        saLoadScript("//www.googlecommerce.com/trustedstores/gtmp_compiled.js");
                    } 
                    
                    return;
                }
            }        
        } 
    }
    //Else they don't have GTS    
}