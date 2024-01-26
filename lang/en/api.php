<?php

return [
    'user' => [
        'incorrect_password' => 'Incorrect Password',
        'incorrect_old_password' => 'Incorrect old Password',
        'change_password' => 'Required is new password should not be same as old password',
        'password_updated' => 'Password Updated',
        'location_updated' => 'Location Updated',
        'language_updated' => 'Language Updated',
        'profile_updated' => 'Profile Updated',
        'user_not_found' => 'User Not Found',
        'not_paid' => 'User Not Paid',
        'referral_amount' => 'Referral Amount',
        'referral_count' => 'Referral Count',
        'invite_friends' => "<p style='color:##FDFEFE;'>Refer your " . config('constants.referral_count', '0') . " friends<br>and earn <span style='color:#f06292'>" . config('constants.currency', '') . config('constants.referral_amount', '0') . "</span> per head</p>"
    ],
    'provider' => [
        'incorrect_password' => 'Incorrect Password',
        'incorrect_old_password' => 'Incorrect old Password',
        'change_password' => 'Required is new password should not be same as old password',
        'password_updated' => 'Password Updated',
        'location_updated' => 'Location Updated',
        'language_updated' => 'Language Updated',
        'profile_updated' => 'Profile Updated',
        'provider_not_found' => 'Provider Not Found',
        'not_approved' => 'You account has not been approved for driving',
        'incorrect_email' => 'The email address or password you entered is incorrect',
        'referral_amount' => 'Referral Amount',
        'referral_count' => 'Referral Count',
        'invite_friends' => "<p style='color:##FDFEFE;'>Refer your " . config('constants.referral_count', '0') . " friends<br>and earn <span style='color:#f06292'>" . config('constants.currency', '') . config('constants.referral_amount', '0') . "</span> per head</p>"

    ],

    'ride' => [
        'request_inprogress' => 'Already Request accepted',
        'no_providers_found' => 'No Drivers Found',
        'request_cancelled' => 'Your Ride Cancelled',
        'already_cancelled' => 'Already Ride Cancelled',
        'ride_cancelled' => 'Ride Cancelled',
        'already_onride' => 'Already You are Onride',
        'provider_rated' => 'Driver Rated',
        'request_scheduled' => 'Ride Scheduled',
        'request_already_scheduled' => 'Ride Already Scheduled',
        'request_modify_location' => 'User Changed Destination Address',
        'request_completed' => 'Request Completed',
        'request_not_completed' => 'Request not yet completed',
        'request_rejected' => 'Request Rejected Successfully',
        'no_service_found' => 'No Service Found',
        'payment_updated' => 'Payment updated',
        'request_accepted' => 'Your Ride Accepted',
    ],
    'service' => [
        'already_cancelled' => 'Already Service Cancelled',
        'request_inprogress' => 'Already Request in Progress',
        'request_rejected' => 'Request Rejected Successfully',
        'ride_cancelled' => 'Service Cancelled',
        'request_completed' => 'Request Completed',
        'request_not_completed' => 'Request not yet completed',
        'service_rated' => 'Service Rated',
        'payment_updated' => 'Payment updated',
        'request_accepted' => 'Your Service Accepted',
        'service_not_available_location' => 'Service not available in this city',
    ],
    'order' => [
        'request_inprogress' => 'Already Request in Progress',
        'request_rejected' => 'Request Rejected Successfully',
        'ride_cancelled' => 'Order Cancelled',
        'request_completed' => 'Request Completed',
        'request_not_completed' => 'Request not yet completed',
        'service_rated' => 'Order Successfully Rated',
        'payment_updated' => 'Payment updated',
        'Cancelled' => 'Store Cancelled',
        'something_went_wront' => 'Something Went Wrong',
        'request_accepted' => 'Your Order Accepted',
    ],
    'payment_success' => 'Payment Success',
    'invalid' => 'Invalid credentials',
    'unauthenticated' => 'Unauthenticated',
    'something_went_wrong' => 'Something Went Wrong',
    'destination_changed' => 'Destination location changed',
    'unable_accept' => 'Unable to accept, Please try again later',
    'connection_err' => 'Connection Error',
    'logout_success' => 'Logged out Successfully',
    'email_available' => 'Email Available',
    'email_not_available' => 'Email Not Available',
    'mobile_exist' => 'Mobile Number Already Exists',
    'country_code' => 'Country code is required.',
    'email_exist' => 'Email Already Exists',
    'available' => 'Data Available',
    'services_not_found' => 'Services Not Found',
    'promocode_applied' => 'Promocode Applied',
    'promocode_expired' => 'Promocode Expired',
    'promocode_already_in_use' => 'Promocode Already in Use',
    'paid' => 'Paid',
    'amount_added_to_your_wallet' => 'Amount Added to your Wallet',
    'added_to_your_wallet' => 'Added to your Wallet',
    'amount_success' => 'Request amount added',
    'amount_cancel' => 'Request has been cancelled',
    'amount_max' => 'The amount may not be greater than ',
    'card_already' => 'Card Already Added',
    'card_added' => 'Card Added',
    'card_deleted' => 'Card Deleted',
    'otp' => 'Otp Is Wrong',
    'add_card_required' => 'No Card Available! Please Add your card in your Profile',
    'push' => [
        'request_accepted' => 'Your Ride Accepted by a Driver',
        'service_accepted' => 'Your Service Accepted by a Provider',
        'order_accepted' => 'Your Order Accepted by a Delivery boy',
        'request_assign' => 'Admin has Assigned a Order',
        'arrived' => 'Driver Arrived at your Location',
        'reached' => 'Delivery boy reached shop',
        'pickedup' => 'Ride Started',
        'complete' => 'Ride Completed',
        'rate' => 'Rated Successfully',
        'dropped' => 'Your ride is completed successfully. you have to pay',
        'incoming_request' => 'New Incoming Ride',
        'chat_message' => 'New message received',
        'added_money_to_wallet' => ' Added to your Wallet',
        'charged_from_wallet' => ' Charged from your Wallet',
        'document_verfied' => 'Your Documents are verified, Now you are ready to Start your Business',
        'provider_not_available' => 'Sorry for inconvenience time, Our partners are busy. Please try after some time',
        'user_cancelled' => 'User Cancelled the Ride',
        'provider_cancelled' => 'Driver Cancelled the Ride',
        'schedule_start' => 'Your schedule ride has been started',
        'provider_waiting_start' => 'Driver Started the Waiting Time',
        'provider_waiting_end' => 'Driver Stopped the Waiting Time',
        'provider_status_hold' => 'Go Offline if you want to have a rest',
        'provider_documents_notify' => 'Your Documents are going to Expire. Please Upload the new documents',
        'service' => [
            'incoming_request' => 'New Incoming Service Request',
            'provider_not_available' => 'Sorry for inconvenience time, Our partners are busy. Please try after some time',
            'arrived' => 'Arrived at your Location',
            'pickedup' => 'Service Started',
            'complete' => 'Service Completed',
            'dropped' => 'Your service is completed successfully. you have to pay',
            'confirmpay' => 'Payment Confirmed',
        ],
        'order' => [
            'incoming_request' => 'New Incoming Order Request',
            'provider_not_available' => 'Sorry for inconvenience time, Our partners are busy. Please try after some time',
            'started' => 'Delivery boy started towards shop',
            'reached' => 'Delivery boy reached shop',
            'arrived' => 'Delivery boy Arrived at your Location',
            'pickedup' => 'Order Picked up',
            'complete' => 'Order Completed',
            'verified' => 'Order OTP verified',
            'dropped' => 'Your service is completed successfully. you have to pay',
            'confirmpay' => 'Payment Confirmed',
            'already_completed' => 'Order already completed',
        ],
    ],
    'transaction' => [
        'admin_commission' => 'admin commission',
        'fleet_debit' => 'fleet commission debited',
        'fleet_add' => 'fleet commission added',
        'fleet_recharge' => 'fleet commission recharge',
        'discount_apply' => 'discount applied',
        'discount_refund' => 'discount amount refund',
        'discount_recharge' => 'provider discount amount recharge',
        'tax_credit' => 'tax amount credited',
        'tax_debit' => 'tax amount debited',
        'provider_credit' => 'ride amount added',
        'provider_recharge' => 'provider ride amount recharge',
        'user_recharge' => 'recharge',
        'user_trip' => 'trip',
        'referal_recharge' => 'Referal recharge',
        'dispute_refund' => 'Dispute refund',
        'peak_commission' => 'Peak hours commission',
        'waiting_commission' => 'Waiting charges commission',
    ],
];
