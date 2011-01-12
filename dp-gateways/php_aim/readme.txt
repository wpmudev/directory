--------------------------------------------------------------------------------
DOCUMENTATION
--------------------------------------------------------------------------------

This code sample has been successfully tested on the following server
configurations and performed according to Authorize.Net's documented Advanced
Integration Method (AIM) standards.
  Microsoft-IIS/5.1 using PHP Version 5.2.8
  Apache/2.2.9 using PHP version 5.2.6 on Windows
  Apache/2.2.3 using PHP version 5.2.3 on Linux

Last updated July 2010

For complete documentation, please visit the Authorize.Net developer website at:
http://developer.authorize.net

--------------------------------------------------------------------------------
DISCLAIMER
--------------------------------------------------------------------------------

WARNING: ANY USE BY YOU OF THE SAMPLE CODE PROVIDED IS AT YOUR OWN RISK

Authorize.Net provides this code "as is" without warranty of any kind, either
express or implied, including but not limited to the implied warranties of
suitability and/or fitness for a particular purpose.

This sample code is provided merely as a blueprint demonstrating one possible
approach to integrating with Authorize.Net using our Server Integration
Method.

This sample code is not a tutorial.  If you are unfamiliar with specific
programming functions and concepts, please consult the appropriate reference
materials.

--------------------------------------------------------------------------------
PREREQUISITES
--------------------------------------------------------------------------------

- In order to establish a successful connection to Authorize.Net, it is required
  that you obtain a valid API Login ID and Transaction Key and enter these
  values into the appropriate places within this sample code.  This is obtained
  within the settings section of your Authorize.Net account.  If you do not have
  an Authorize.Net account or prefer not to use it for testing, you can obtain
  a free developer test account by using the form at:
  http://developer.authorize.net

- This sample code uses the PHP Curl module in order to establish a connection
  to the Authorize.net server.  The Curl module must be available for this code
  to function.  Additional documentation on using the curl module can be found
  at: http://www.php.net/manual/en/book.curl.php

--------------------------------------------------------------------------------
TROUBLESHOOTING
--------------------------------------------------------------------------------

ERROR: 
  13 - The merchant Login ID is invalid or the account is inactive.

RESOLUTION:
  This error is caused by one of two things.
	
  First, the "API Login ID" entered	is invalid. Please ensure that you have
  updated the sample code with your valid API Login ID. (see PREREQUISITES)
	
  Second, the wrong posting URL is being used. For developer accounts, ensure
  that you are posting to: https://test.authorize.net/gateway/transact.dll
  For live accounts (even in test mode) ensure that you are posting to:
  https://secure.authorize.net/gateway/transact.dll
	
ERROR:
  103 - This transaction cannot be accepted.
	
RESOLUTION:
  This error indicates that an invalid Transaction Key has been submited.
  
  You will want to verify that you are using the most recently generated
  Transaction Key from your account.  Please keep in mind that whenever a
  new transaction key is generated, previous keys will be disabled.
  
ERROR:
  123 - This account has not been given the permissions required for this request.
  
RESOLUTION:
  This error occurs when the incorrect Login ID is being submitted.  You will
  want to make sure that you are using the "API Login ID" obtained with your
  account settings.
  
  The API login ID is not the same login that you use for accessing your
  Authorize.Net administrative interface.
  
DEVELOPER TOOLS:
  Authorize.Net's developer site provides tools for helping diagnose any error
  that you may encounter.  Here are some tools that may help you.
  
 RESPONSE REASON CODE TOOL:
  This tool can be used to look up the meaning of any error code returned by
  the Authorize.Net SIM or AIM APIs.  The most common errors also include
  troubleshooting steps:
  http://developer.authorize.net/tools/responsereasoncode/

 DATA VALIDATION URL TOOL:
  The data validation tool allows you to double check all of the data that you
  are sending to Authorize.Net with your AIM request.  This can often make it
  easy to see errors that are otherwise difficult to locate:
  http://developer.authorize.net/tools/datavalidation/
  
 DOCUMENTATION:
  Documentation for all of our integration methods can be found on our
  developer site.  Please review this documentation for additional
  details on the integration process:
  http://developer.authorize.net/guides/
  
OTHER ERRORS:
  If all else fails, you can contact our integration support department at:
  integration@authorize.net
  
  Please provide clear details on the error that you are receiving and the
  steps that you are taking to produce the error.  Also remember that we cannot
  support individual e-commerce developers with programming problems and other
  issues that are not directly caused by, or related to, our APIs.