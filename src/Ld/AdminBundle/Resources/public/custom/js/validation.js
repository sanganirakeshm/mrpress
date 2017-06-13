$(document).ready(function() {
        
       $.validator.addMethod("regex_username", function(value, element, regexpr) {
            return regexpr.test(value);
        }, "Your username cannot contain a period or @. It can contain characters, numbers and these special characters only - _ ! # $.");

        $.validator.addMethod("regex_password", function(value, element, regexpr) {
            return regexpr.test(value);
        }, "Your current password contains invalid characters. Passwords can contain characters, numbers and only these special characters: ! @ # $");

        $('#add_cust_form').validate({
            rules: {
            	"ld_customer_registration[firstname]": {
                    "required": true
                },
            	"ld_customer_registration[lastname]": {
                    "required": true
                },
                "ld_customer_registration[username]": {
                    "required": true,
                    "regex_username": /^[A-Za-z0-9-_!#$]+$/,
                    "minlength": 6,
                    "maxlength": 32,
                },
                "ld_customer_registration[email]": {
                    "required": true,
                    "email": true
                },
                "ld_customer_registration[plainPassword][first]": {
                    "required": true,
                    "regex_password": /^[A-Za-z0-9!@#$_]+$/,
                    "minlength": 8,
                    "maxlength": 18,
                },
                "ld_customer_registration[plainPassword][second]": {
                    "required": true,
                    "equalTo": "#ld_customer_registration_plainPassword_first"
                },
                "ld_customer_registration[groups][]" : {
                     "required" : true   
                },
                "ld_customer_registration[enabled]" : {
                     "required" : true   
                },

            },
            messages: {
            	"ld_customer_registration[firstname]": {
                    "required": "Please enter firstname."                  
                },
                "ld_customer_registration[lastname]": {
                    "required": "Please enter lastname."                  
                },
                "ld_customer_registration[username]": {
                    "required": "Please enter username.",
                    "minlength": "Your username must have minimum 6 characters.",
                    "maxlength": "Your username can have maximum 32 characters."
                  
                },
                "ld_customer_registration[email]": {
                    "required": "Please enter an email.",
                    "email": "Please enter valid email."
                },
                "ld_customer_registration[plainPassword][first]": {
                    "required": "Please enter password.",
                    "minlength": "Your password must have minimum 8 characters.",
                    "maxlength": "Your password can have maximum 18 characters."
                },
                "ld_customer_registration[plainPassword][second]": {
                    "required": "Please enter confirm password.",
                    "equalTo": "Password does not match the confirm password."
                },
                "ld_customer_registration[groups][]" : {
                     "required" : "Please select group."   
                },
                "ld_customer_registration[enabled]" : {
                     "required" : "Please select status."   
                },
            }
        });   
        
        $('#edit_cust_form').validate({
            rules: {
            	"ld_customer_registration[firstname]": {
                    "required": true
                },
            	"ld_customer_registration[lastname]": {
                    "required": true
                },
                "ld_customer_registration[username]": {
                    "required": true,
                    "regex_username": /^[A-Za-z0-9-_!#$]+$/,
                    "minlength": 6,
                    "maxlength": 32,
                },
                "ld_customer_registration[email]": {
                    "required": true,
                    "email": true
                },
                "ld_customer_registration[plainPassword][first]": {
                    "required": true,
                    "regex_password": /^[A-Za-z0-9!@#$_]+$/,
                    "minlength": 8,
                    "maxlength": 18,
                },
                "ld_customer_registration[plainPassword][second]": {
                    "required": true,
                    "equalTo": "#ld_customer_registration_plainPassword_first"
                },
                "ld_customer_registration[groups][]" : {
                     "required" : true   
                },
                "ld_customer_registration[enabled]" : {
                     "required" : true   
                },

            },
            messages: {
            	"ld_customer_registration[firstname]": {
                    "required": "Please enter firstname."                  
                },
                "ld_customer_registration[lastname]": {
                    "required": "Please enter lastname."                  
                },
                "ld_customer_registration[username]": {
                    "required": "Please enter username.",
                    "minlength": "Your username must have minimum 6 characters.",
                    "maxlength": "Your username can have maximum 32 characters."
                  
                },
                "ld_customer_registration[email]": {
                    "required": "Please enter an email.",
                    "email": "Please enter valid email."
                },
                "ld_customer_registration[plainPassword][first]": {
                    "required": "Please enter password.",
                    "minlength": "Your password must have minimum 8 characters.",
                    "maxlength": "Your password can have maximum 18 characters."
                },
                "ld_customer_registration[plainPassword][second]": {
                    "required": "Please enter confirm password.",
                    "equalTo": "Password does not match the confirm password."
                },
                "ld_customer_registration[groups][]" : {
                     "required" : "Please select group."   
                },
                "ld_customer_registration[enabled]" : {
                     "required" : "Please select status."   
                },
            }
        });
        
        $('#change_password_cust').validate({
            rules: {            	
                "ld_user_changepassword[plainPassword][first]": {
                    "required": true,
                    "regex_password": /^[A-Za-z0-9!@#$_]+$/,
                    "minlength": 8,
                    "maxlength": 18,
                },
                "ld_user_changepassword[plainPassword][second]": {
                    "required": true,
                    "equalTo": "#ld_user_changepassword_plainPassword_first"
                }
            },
            messages: {            	
                "ld_user_changepassword[plainPassword][first]": {
                    "required": "Please enter password.",
                    "minlength": "Your password must have minimum 8 characters.",
                    "maxlength": "Your password can have maximum 18 characters."
                },
                "ld_user_changepassword[plainPassword][second]": {
                    "required": "Please enter confirm password.",
                    "equalTo": "Password does not match the confirm password."
                }
            }
        });
        
        
        $('#add_permission_form, #edit_permission_form').validate({
            rules: {
            	"ld_admin_permission[name]": {
                    "required": true
                },
            	"ld_admin_permission[code]": {
                    "required": true
                },
                "ld_admin_permission[status]": {
                    "required": true
                },
                "ld_admin_permission[category]" : {
                    "required" : true   
                },

            },
            messages: {
            	"ld_admin_permission[name]": {
                    "required": "Please enter name."                  
                },
                "ld_admin_permission[code]": {
                    "required": "Please enter code."                  
                },                
                "ld_admin_permission[status]": {
                    "required": "Please select status."
                },
                "ld_admin_permission[category]" : {
                     "required" : "Please select permission category."   
                },
            }
        });
        
        $('#add_permission_category_form, #edit_permission_category_form').validate({
            rules: {
            	"ld_admin_permission_category[name]": {
                    "required": true
                },
                "ld_admin_permission_category[status]": {
                    "required": true
                }

            },
            messages: {
            	"ld_admin_permission_category[name]": {
                    "required": "Please enter name."                  
                },                
                "ld_admin_permission_category[status]": {
                    "required": "Please select status."
                }
            }
        });
        
    });