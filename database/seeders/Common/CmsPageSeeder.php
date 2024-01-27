<?php

namespace Database\Seeders\Common;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CmsPageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run($company = null): void
    {
        Schema::disableForeignKeyConstraints();

        DB::table("cms_pages")->insert([
            [
                "company_id" => $company,
                "page_name" => "help",
                "content" =>
                "<p>Hyperjek will provide you with support services that are applicable to the Software Product. Use of any such support services is governed by Hyperjek policies discussed in online documentation and/or other Hyperjek documentation.</p><p>Any additional software code or related information that Uberlikeapp provides you as part of support services is to be considered part of the Software Product and is dependent on the terms and conditions of this EULA.</p><p>Any technical information that you provide to Uberlikeapp with respect to support services can be used by Uberlikeapp for business purposes without restriction, including for product support and development. Uberlikeapp will not use such technical information in any manner that can personally identify you.</p>",
                "status" => 1,
            ],
            [
                "company_id" => $company,
                "page_name" => "terms",
                "content" =>
                "<h2><strong>Terms &amp; Conditions</strong></h2><p>These terms and conditions (&ldquo;T&amp;Cs&rdquo;) apply to Your access to, and use of the Application (defined hereinbelow). The Application is operated by Hyperjek &nbsp;and its subsidiaries, associates, licensees, and affiliated companies &lsquo;&rsquo;Uberlikeapp&rdquo;.</p><p>You acknowledge that Uberlikeapp is providing you with a revocable license to use limited features of the Application and is not selling the Application or any features or technologies contained therein.</p><p>By continuing usage of the Application, You are consenting to be bound by these T&amp;Cs.</p><p><strong>PLEASE ENSURE THAT YOU READ AND UNDERSTAND ALL THESE T&amp;Cs BEFORE YOU USE THE APPLICATION AND FEATURES CONTAINED THEREIN.</strong></p><p>If You find any of the T&amp;Cs unacceptable, do not tender your acceptance to use the Application or avail any of its features. YOUR AGREEMENT TO THESE T&amp;Cs SHALL OPERATE AS A BINDING AND LEGALLY ENFORCEABLE AGREEMENT BETWEEN YOU AND ANI IN RESPECT OF THE FEATURES OFFERED/AVAILED USING THE MOBILE APPLICATION.</p><p><strong>I. DEFINITIONS</strong></p><p>All of the defined and capitalized terms in these T&amp;Cs will have the meaning assigned to them here below:</p><ol>   <li>  <p>&ldquo;Account&rdquo; refers to the User&rsquo;s/Provider&rsquo;s account on the mobile application Go-x enabling the use of the Application by the User</p>   </li> <li>  <p>&ldquo;Application&rdquo; shall mean the mobile application Go-X and shall mean and include any updates provided by Uberlikeapp from time to time.</p> </li> <li>  <p>&ldquo;Registration Data&rdquo; shall mean and may include the present, valid, true and accurate name, Email ID, phone number and such other information as may be required by Hyperjek from time to time, provided by the Users at the time of registration on the mobile application Hyperjek or otherwise.</p>    </li> <li>  <p>&ldquo;User&rdquo; shall mean persons who have created an Account and use the Application.</p> </li> <li>  <p>&quot;You&quot;, &ldquo;Your&quot; or &ldquo;Yourself&rdquo; shall mean reference to the User accessing the Application</p>    </li></ol>",
                "status" => 1,
            ],
            [
                "company_id" => $company,
                "page_name" => "page_privacy",
                "content" =>
                "<h2><strong>Privacy Policy</strong></h2><p><strong>This Privacy Policy sets out the manner in which will collect, hold and protect information about you when you use our website and /or mobile application to test our Demo Apps.</strong></p><p><strong>a. We may disclose to third party services certain personally identifiable information listed below:</strong></p><p><strong>- Information you provide us such as below :</strong></p><ul>   <li>  <p><strong>Name</strong></p>  </li> <li>  <p><strong>Email</strong></p> </li> <li>  <p><strong>Mobile phone number</strong></p>   </li> <li>  <p><strong>Country and city</strong></p>  </li> <li>  <p><strong>Demographic information</strong></p>   </li> <li>  <p><strong>Information we collect as you access and use our service, including device information, location and network carrier</strong></p>  </li></ul><p><strong>- This information is shared with third party service providers so that we can:</strong></p><ul>   <li>  <p><strong>Personalize the app for you</strong></p>   </li> <li>  <p><strong>Perform behavioral analytics</strong></p>  </li> <li>  <p><strong>Improve our products and services</strong></p> </li></ul><p><strong>To periodically send promotional emails about new products, special offers or other information which we think you may find interesting using the email address/Mobile number which you have provided.</strong></p><p><strong>b. Security</strong></p><ul> <li>  <p><strong>We are committed to ensuring that your information is secure.</strong></p> </li> <li>  <p><strong>In order to prevent unauthorized access or disclosure we have put in place suitable physical, electronic and managerial procedures to safeguard and secure the information we collect online.</strong></p> </li></ul><p><strong>c. Third Party Website Links</strong></p><ul>  <li>  <p><strong>Our website may contain links to other websites. However, once you have used these links to leave our site, you should note that we do not have any control over those websites. Therefore, we cannot be responsible for the protection and privacy of any information which you provide to those websites.</strong></p>   </li> <li>  <p><strong>We will not sell, distribute or lease your personal information to third parties unless we have your permission or are required by law to do so.</strong></p>  </li> <li>  <p><strong>If you believe that any information we are holding on you is incorrect or incomplete, please write to or email us as soon as possible at the info@uberlikeapp.com email address. We will promptly correct any information found to be incorrect.</strong></p>  </li></ul><p><strong>Cookies</strong></p><p><strong>Hyperjek website may use cookies to assist in producing overall site visitor statistics. Cookies, by themselves, cannot be used to find out the identity of any user unless they specifically tell us who they are. If you wish to, you can disable cookies on your computer by changing the settings in preferences or options menu in your browser.</strong></p><h3><strong>Disclosure Of User Information</strong></h3><p><strong>Hyperjek.com does not rent, sell, or share personal information about you with non-affiliated party or companies. If you have submitted user information to us through an e-mail, Hyperjek.com maintains your security by ensuring that the information is only distributed within the Hyperjek.com Group who are all responsible for responding to your requests either directly or indirectly.</strong></p><h3><strong>We May Disclose Information In The Following Circumstances</strong></h3><ul>  <li>  <p><strong>We provide the information to trusted partners who work on behalf of or with GO-X under extremely strict confidentiality agreements.</strong></p>  </li> <li>  <p><strong>We respond to subpoenas, court orders, or legal process, or to establish or exercise our legal rights or defend against legal claims.</strong></p> </li> <li>  <p><strong>We believe it is necessary to share information in order to investigate, prevent, or take action regarding illegal activities, suspected fraud, situations involving potential threats to the physical safety of any person, or as otherwise required by law.</strong></p> </li></ul><h3><strong>Disclosure Of User Information</strong></h3><p><strong>The products, technology and services described in this site may be the subject of intellectual property rights reserved by Hyperjek or other third parties. Nothing contained herein shall be construed as conferring to you in any manner, whether by implication, estoppel or otherwise, any license, title or ownership of or to any intellectual property right or any third party.</strong></p>",
                "status" => 1,
            ],
            [
                "company_id" => $company,
                "page_name" => "cancel",
                "content" =>
                "<p>Uberlikeapp kindly requests you to take ample time to evaluate the software through our online demo, prior to making a purchase decision. Before making the purchase, ensure that your server supports the necessary software requirements. Refunds will not be provided on the basis of user&rsquo;s lack of knowledge of the software&rsquo;s functionality, limitations, or restrictions, as Uberlikeapp provides a comprehensive evaluation of the software by means of our online demo.</p><p>If you have any queries about the software product, please get in touch with our staff before making a decision. The entire source code is shipped with the software. The digital characteristic of the product makes it non-returnable, so sales are final. Refunds are not provided for installation charges or other non-labor related charges.</p>",
                "status" => 1,
            ],
            [
                "company_id" => $company,
                "page_name" => "legal",
                "content" =>
                "<h3><strong>Governing Law</strong></h3><p>These Terms shall be governed and construed in accordance with the laws of Tamil Nadu, India, without regard to its conflict of law provisions.</p><p>Our failure to enforce any right or provision of these Terms will not be considered a waiver of those rights. If any provision of these Terms is held to be invalid or unenforceable by a court, the remaining provisions of these Terms will remain in effect. These Terms constitute the entire agreement between us regarding our Service, and supersede and replace any prior agreements we might have between us regarding the Service.</p><h3><strong>Change</strong></h3><p>We reserve the right, at our sole discretion, to modify or replace these Terms at any time. If a revision is material we will try to provide at least 30 days notice prior to any new terms taking effect. What constitutes a material change will be determined at our sole discretion.</p><p>By continuing to access or use our Service after those revisions become effective, you agree to be bound by the revised terms. If you do not agree to the new terms, please stop using the Service.</p>",
                "status" => 1,
            ],
            [
                "company_id" => $company,
                "page_name" => "about_us",
                "content" =>
                "<h2><strong>About Us</strong></h2><p>The Application is the great way of travelling, servicing and ordering all with your smart phone and all in a single tap. Tap and look for your ride/service/order, Tap and book your ride/service/order and Tap and complete your ride/service/order . Your tap brings the driver to you for the best riding experience of your life, each one better than the previous one.No hassles of direction giving as your drivers know exactly where you want to go. Payment completed via your Cash/Card or any dynamic payment gateway. Just sit back and enjoy your ride/service and order in your own way!!</p>",
                "status" => 1,
            ],
            [
                "company_id" => $company,
                "page_name" => "faq",
                "content" =>
                '<p><strong>How Easy Is It To Setup A Business With Your Products?</strong></p><p>What do you need? &ndash; An idea, The product that captures the idea and some business stuff to launch it. We are pulling your weight with the product, you have the idea to use it specifically for a target. Besides, We&rsquo;ll plug you in with the necessary tools like free hosting, chat tools, help desk software and the host of other precious stuff worth over $20000 to sweeten your life. Just launch the damn thing and watch your networth, Will ya?! ðŸ™‚</p><p><strong>What About Customization?</strong></p><p>We know where you come from.. It&#39;s never enough for you entrepreneurs! Yes!, tell us what needs changed in the standard product and show us the way and we&rsquo;ll walk it for you.</p>',
                "status" => 1,
            ],
        ]);

        Schema::enableForeignKeyConstraints();
    }
}
