<?php

//require_once('product-functions');
global $wpdb;
$site_url = site_url();
$current_user = wp_get_current_user();
$currentuserid = $current_user->ID;
$currentusername = $current_user->user_login;

// Only show if logged in
if ($currentuserid == 0) {
wp_redirect("/login");
exit();
}

get_header();
?>

<div class="container-fluid dashboard_content my_brand">
    <div class="row">
        <?php include('sidebar.php'); ?>
        <div class="col py-80">
            <img class="dashboard_graphic dashboard_graphic_default" src="<?php echo get_template_directory_uri(); ?>/images/dashboard_graphic_default.png">
            <div class="row">
                <div class="col-lg-7 col-xl-6">
                    <h1 class="fs2"><svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="120" height="120" viewBox="0 0 120 120"><ellipse style="opacity:0.5; fill:#19CBC5;" cx="60" cy="60" rx="60" ry="60"/><g transform="translate(14.374 14.374)"><ellipse style="fill:#19CBC5;" cx="45.6" cy="45.6" rx="45.6" ry="45.6"/></g><g transform="translate(59.729 22.971) rotate(31)"><path style="fill:none;stroke:#361181;stroke-width:2;" d="M40.8,37.6C37.4,41,29,37.5,20.7,29.3S9,12.5,12.4,9.1s11.9,0.1,20.1,8.3S44.2,34.2,40.8,37.6z M31.1,46.7c-1.5-0.6-3.2,0.8-4.4,0.5c-1.9-0.6-2.3-0.9-3.9-1.4c-1-0.6-2.3-0.3-3.1,0.5L16,50c3.9,1.3,9.3,8,12.5,4.7l3.5-3.9C33.4,49.5,34.3,48,31.1,46.7L31.1,46.7z M18.7,39.8c-4.2-1.1-7.4-4.4-8.5-8.6c1.6-2.4,2.5-5.1,2.8-8c-1.3-1.9-2.3-4-3-6.1C10,21,10.4,26.5,4.6,32.2l-3.6,3.6c-2.2,2.2-1,6.9,2.6,10.5c3.6,3.6,8.3,4.8,10.5,2.6l3.6-3.6l0,0c5.7-5.7,11-5.5,14.9-5.5c-2.1-0.7-4-1.7-5.9-2.9C23.9,37.2,21.1,38.2,18.7,39.8L18.7,39.8z M27.6,22.3c-2.5-2.5-4.8-1.4-5.7-0.7c0.9,1.1,1.9,2.2,3.1,3.3c1.1,1.1,2.2,2.1,3.3,3C29.1,27.2,30.1,24.8,27.6,22.3L27.6,22.3z M37.2,14.7c-0.6,0.5-1.5,0.5-2-0.1c-0.5-0.5-0.5-1.3,0-1.8c0,0,4.4-4.9,4.4-4.9c0.5-0.5,1.4-0.5,2,0c0,0,0,0,0,0l0.5,0.5c0.5,0.5,0.5,1.4,0,2C42.1,10.3,37.2,14.7,37.2,14.7L37.2,14.7z M28,8c-0.4,0.7-1.2,0.9-1.9,0.5c-0.6-0.4-0.9-1.1-0.6-1.7c0,0,2.6-6,2.6-6c0.3-0.7,1.2-1,1.9-0.6l0.7,0.3c0.7,0.3,1,1.2,0.6,1.9c0,0,0,0,0,0C31.3,2.4,28,8,28,8L28,8z M41.9,21.9c-0.7,0.4-0.9,1.2-0.5,1.9c0.4,0.6,1.1,0.8,1.7,0.6l6-2.6c0.7-0.3,1-1.2,0.6-1.9l-0.3-0.7c-0.3-0.7-1.2-1-1.9-0.6c0,0,0,0,0,0C47.6,18.7,41.9,21.9,41.9,21.9L41.9,21.9z M16,12.8c-1.1,2.9,3.2,9.2,7.6,13.6c3.9,3.9,10.5,8.8,13.6,7.6c1.1-2.9-3.2-9.2-7.6-13.6C25.8,16.4,19.2,11.5,16,12.8z"/></g></svg>Orders</h1>
                    <h2 class="fs1">Manual Order</h2>
                    <h3 class="fs2">Get Started</h3>
                    <p>Nullam faucibus ut lectus vitae posuere. Nullam sollicitudin nunc ipsum, quis malesuada orci rutrum sit amet. Maecenas nulla justo, rutrum id iaculis at, elementum non lacus. Proin non molestie erat, vitae mattis elit. Donec ac euismod metus, ut efficitur nibh. Suspendisse magna est, pharetra sed hendrerit sit amet, blandit nec enim. Donec porttitor neque nec consectetur faucibus. Aliquam pellentesque leo eleifend, dictum turpis vitae, mattis mi. Curabitur luctus tortor ligula, vitae ultricies diam viverra id. Maecenas vitae sem quis nulla mollis euismod. Ut et cursus risus. Morbi pretium mi sit amet consectetur porta. Donec mi urna, accumsan eu augue sed, laoreet maximus eros. Maecenas sodales mi justo, vitae luctus purus semper vitae.</p>
                </div>
			</div>
			<div class="col-12 col-lg-6 p-0 text-center">
				<h2 class="fs3">NEW ORDER</h2>
				<svg version="1.1" xmlns="http://www.w3.org/2000/svg" width="36" height="17" viewBox="0 0 35.1 16.8"><path style="fill:none;stroke:#DEDEDE;stroke-width:4;stroke-linecap:round;" d="M2,2l15.7,12.3L33.1,2"/></svg>
			</div>
        </div>
    </div>
</div>

<div class="container-fluid py-40">
    <div class="row align-items-center justify-content-center justify-content-lg-between">
        <div class="col-12 py-40">
            <form class="manual-orders">
                <h2 class="fs2 mb-2">Your Information</h2>
                <p class="mb-4">* Required</p>
                <div class="row">
                    <div class="col-12 col-md-6 col-lg-4">
                        <div class="input_outline">
                            <label for="">Business Name*</label>
                            <input type="text" id="" name="" placeholder="Business Name">
                        </div>
                    </div>
                    <div class="col-12 col-md-6 col-lg-4">
                        <div class="input_outline">
                            <label for="">Business Contact Name*</label>
                            <input type="text" id="" name="" placeholder="Business Contact Name">
                        </div>
                    </div>
                    <div class="col-12 col-md-6 col-lg-4">
                        <div class="input_outline">
                            <label for="">Your Email*</label>
                            <input type="email" id="" name="" placeholder="Your Email">
                        </div>
                    </div>
                </div>
                
                <h3 class="fs3">Return Label</h3>
                <p class="mb-4">You can change the return label from <a href="#" target="_blank">here</a><br> Solution Agency<br> 2 Wurz Avenue<br> Yorkville, NY 13495<br></p>
                <h2 class="fs2 my-4">Order Details</h2>
                <div class="row align-items-end">
                    <div class="col-12 col-md-6 col-lg-4">
                        <div class="input_outline">
                            <label for="">Order ID*</label>
                            <p><i>The Order ID is for your reference and will appear on your order packing slip. Enter whatever you want.</i></p>
                            <input type="text" id="" name="" placeholder="Order ID">
                        </div>
                    </div>
                    <div class="col-12 col-md-6 col-lg-4">
                        <div class="input_outline">
                            <label for="">Sample Voucher</label>
                            <p><a href="#" target="_blank"><i>View More Information</i></a></p>
                            <input type="text" id="" name="" placeholder="Sample Voucher">
                        </div>
                    </div>
                </div>
                <h2 class="fs2 my-4">Shipping Address</h2>
                <div class="row">
                    <div class="col-12 col-md-6 col-lg-4">
                        <div class="input_outline">
                            <label for="">Customer Name*</label>
                            <input type="text" id="" name="" placeholder="Customer Name">
                        </div>
                    </div>
                    <div class="col-12 col-md-6 col-lg-4">
                        <div class="input_outline">
                            <label for="">Address 1*</label>
                            <input type="text" id="" name="" placeholder="Address 1">
                        </div>
                    </div>
                    <div class="col-12 col-md-6 col-lg-4">
                        <div class="input_outline">
                            <label for="">Address 2*</label>
                            <input type="text" id="" name="" placeholder="Address 2">
                        </div>
                    </div>
                </div>
                <div class="row align-items-end">
                    <div class="col-12 col-md-6 col-lg-4">
                        <div class="input_outline">
                            <label for="">City*</label>
                            <input type="text" id="" name="" placeholder="City">
                        </div>
                    </div>
                    <div class="col-12 col-md-6 col-lg-4">
                        <div class="input_outline">
                            <label for="">State/Province</label>
                            <input type="text" id="" name="" placeholder="State/Province">
                        </div>
                    </div>
                    <div class="col-12 col-md-6 col-lg-4">
                        <select id="country" name="country"><option value="AF">Afghanistan</option><option value="AX">Åland Islands</option><option value="AL">Albania</option><option value="DZ">Algeria</option><option value="AS">American Samoa</option><option value="AD">Andorra</option><option value="AO">Angola</option><option value="AI">Anguilla</option>
                            <option value="AQ">Antarctica</option><option value="AG">Antigua and Barbuda</option><option value="AR">Argentina</option><option value="AM">Armenia</option><option value="AW">Aruba</option><option value="AU">Australia</option><option value="AT">Austria</option><option value="AZ">Azerbaijan</option><option value="BS">Bahamas</option><option value="BH">Bahrain</option><option value="BD">Bangladesh</option><option value="BB">Barbados</option><option value="BY">Belarus</option>
                            <option value="BE">Belgium</option><option value="BZ">Belize</option><option value="BJ">Benin</option><option value="BM">Bermuda</option><option value="BT">Bhutan</option><option value="BO">Bolivia</option><option value="BA">Bosnia and Herzegovina</option><option value="BW">Botswana</option><option value="BV">Bouvet Island</option><option value="BR">Brazil</option><option value="IO">British Indian Ocean Territory</option><option value="BN">Brunei Darussalam</option><option value="BG">Bulgaria</option>
                            <option value="BF">Burkina Faso</option><option value="BI">Burundi</option><option value="KH">Cambodia</option><option value="CM">Cameroon</option><option value="CA">Canada</option><option value="CV">Cape Verde</option><option value="KY">Cayman Islands</option><option value="CF">Central African Republic</option><option value="TD">Chad</option><option value="CL">Chile</option><option value="CN">China</option><option value="CX">Christmas Island</option><option value="CC">Cocos (Keeling) Islands</option><option value="CO">Colombia</option>
                            <option value="KM">Comoros</option><option value="CG">Congo</option><option value="CD">Congo, The Democratic Republic of The</option><option value="CK">Cook Islands</option><option value="CR">Costa Rica</option><option value="CI">Cote D"ivoire</option><option value="HR">Croatia</option><option value="CU">Cuba</option><option value="CY">Cyprus</option><option value="CZ">Czech Republic</option><option value="DK">Denmark</option><option value="DJ">Djibouti</option><option value="DM">Dominica</option><option value="DO">Dominican Republic</option>
                            <option value="EC">Ecuador</option><option value="EG">Egypt</option><option value="SV">El Salvador</option><option value="GQ">Equatorial Guinea</option><option value="ER">Eritrea</option><option value="EE">Estonia</option><option value="ET">Ethiopia</option><option value="FK">Falkland Islands (Malvinas)</option><option value="FO">Faroe Islands</option><option value="FJ">Fiji</option><option value="FI">Finland</option><option value="FR">France</option><option value="GF">French Guiana</option><option value="PF">French Polynesia</option><option value="TF">French Southern Territories</option><option value="GA">Gabon</option>
                            <option value="GM">Gambia</option>
                            <option value="GE">Georgia</option>
                            <option value="DE">Germany</option>
                            <option value="GH">Ghana</option>
                            <option value="GI">Gibraltar</option>
                            <option value="GR">Greece</option>
                            <option value="GL">Greenland</option>
                            <option value="GD">Grenada</option>
                            <option value="GP">Guadeloupe</option>
                            <option value="GU">Guam</option>
                            <option value="GT">Guatemala</option>
                            <option value="GG">Guernsey</option>
                            <option value="GN">Guinea</option>
                            <option value="GW">Guinea-bissau</option>
                            <option value="GY">Guyana</option>
                            <option value="HT">Haiti</option>
                            <option value="HM">Heard Island and Mcdonald Islands</option>
                            <option value="VA">Holy See (Vatican City State)</option>
                            <option value="HN">Honduras</option>
                            <option value="HK">Hong Kong</option>
                            <option value="HU">Hungary</option>
                            <option value="IS">Iceland</option>
                            <option value="IN">India</option>
                            <option value="ID">Indonesia</option>
                            <option value="IR">Iran, Islamic Republic of</option>
                            <option value="IQ">Iraq</option>
                            <option value="IE">Ireland</option>
                            <option value="IM">Isle of Man</option>
                            <option value="IL">Israel</option>
                            <option value="IT">Italy</option>
                            <option value="JM">Jamaica</option>
                            <option value="JP">Japan</option>
                            <option value="JE">Jersey</option>
                            <option value="JO">Jordan</option>
                            <option value="KZ">Kazakhstan</option>
                            <option value="KE">Kenya</option>
                            <option value="KI">Kiribati</option>
                            <option value="KW">Kuwait</option>
                            <option value="KG">Kyrgyzstan</option>
                            <option value="LA">Lao People"s Democratic Republic</option>
                            <option value="LV">Latvia</option>
                            <option value="LB">Lebanon</option>
                            <option value="LS">Lesotho</option>
                            <option value="LR">Liberia</option>
                            <option value="LY">Libyan Arab Jamahiriya</option>
                            <option value="LI">Liechtenstein</option>
                            <option value="LT">Lithuania</option>
                            <option value="LU">Luxembourg</option>
                            <option value="MO">Macao</option>
                            <option value="MK">Macedonia, The Former Yugoslav Republic of</option>
                            <option value="MG">Madagascar</option>
                            <option value="MW">Malawi</option>
                            <option value="MY">Malaysia</option>
                            <option value="MV">Maldives</option>
                            <option value="ML">Mali</option>
                            <option value="MT">Malta</option>
                            <option value="MH">Marshall Islands</option>
                            <option value="MQ">Martinique</option>
                            <option value="MR">Mauritania</option>
                            <option value="MU">Mauritius</option>
                            <option value="YT">Mayotte</option>
                            <option value="MX">Mexico</option>
                            <option value="FM">Micronesia, Federated States of</option>
                            <option value="MD">Moldova, Republic of</option>
                            <option value="MC">Monaco</option>
                            <option value="MN">Mongolia</option>
                            <option value="ME">Montenegro</option>
                            <option value="MS">Montserrat</option>
                            <option value="MA">Morocco</option>
                            <option value="MZ">Mozambique</option>
                            <option value="MM">Myanmar</option>
                            <option value="NA">Namibia</option>
                            <option value="NR">Nauru</option>
                            <option value="NP">Nepal</option>
                            <option value="NL">Netherlands</option>
                            <option value="AN">Netherlands Antilles</option>
                            <option value="NC">New Caledonia</option>
                            <option value="NZ">New Zealand</option>
                            <option value="NI">Nicaragua</option>
                            <option value="NE">Niger</option>
                            <option value="NG">Nigeria</option>
                            <option value="NU">Niue</option>
                            <option value="NF">Norfolk Island</option>
                            <option value="KP">North Korea</option>
                            <option value="MP">Northern Mariana Islands</option>
                            <option value="NO">Norway</option>
                            <option value="OM">Oman</option>
                            <option value="PK">Pakistan</option>
                            <option value="PW">Palau</option>
                            <option value="PS">Palestinian Territory, Occupied</option>
                            <option value="PA">Panama</option>
                            <option value="PG">Papua New Guinea</option>
                            <option value="PY">Paraguay</option>
                            <option value="PE">Peru</option>
                            <option value="PH">Philippines</option>
                            <option value="PN">Pitcairn</option>
                            <option value="PL">Poland</option>
                            <option value="PT">Portugal</option>
                            <option value="PR">Puerto Rico</option>
                            <option value="QA">Qatar</option>
                            <option value="RE">Reunion</option>
                            <option value="RO">Romania</option>
                            <option value="RU">Russia</option>
                            <option value="RW">Rwanda</option>
                            <option value="SH">Saint Helena</option>
                            <option value="KN">Saint Kitts and Nevis</option>
                            <option value="LC">Saint Lucia</option>
                            <option value="PM">Saint Pierre and Miquelon</option>
                            <option value="VC">Saint Vincent and The Grenadines</option>
                            <option value="WS">Samoa</option>
                            <option value="SM">San Marino</option>
                            <option value="ST">Sao Tome and Principe</option>
                            <option value="SA">Saudi Arabia</option>
                            <option value="SN">Senegal</option>
                            <option value="RS">Serbia</option>
                            <option value="SC">Seychelles</option>
                            <option value="SL">Sierra Leone</option>
                            <option value="SG">Singapore</option>
                            <option value="SK">Slovakia</option>
                            <option value="SI">Slovenia</option>
                            <option value="SB">Solomon Islands</option>
                            <option value="SO">Somalia</option>
                            <option value="ZA">South Africa</option>
                            <option value="GS">South Georgia and The South Sandwich Islands</option>
                            <option value="KR">South Korea</option>
                            <option value="ES">Spain</option>
                            <option value="LK">Sri Lanka</option>
                            <option value="SD">Sudan</option>
                            <option value="SR">Suriname</option>
                            <option value="SJ">Svalbard and Jan Mayen</option>
                            <option value="SZ">Swaziland</option>
                            <option value="SE">Sweden</option>
                            <option value="CH">Switzerland</option>
                            <option value="SY">Syrian Arab Republic</option>
                            <option value="TW">Taiwan, Province of China</option>
                            <option value="TJ">Tajikistan</option>
                            <option value="TZ">Tanzania, United Republic of</option>
                            <option value="TH">Thailand</option>
                            <option value="TL">Timor-leste</option>
                            <option value="TG">Togo</option>
                            <option value="TK">Tokelau</option>
                            <option value="TO">Tonga</option>
                            <option value="TT">Trinidad and Tobago</option>
                            <option value="TN">Tunisia</option>
                            <option value="TR">Turkey</option>
                            <option value="TM">Turkmenistan</option>
                            <option value="TC">Turks and Caicos Islands</option>
                            <option value="TV">Tuvalu</option>
                            <option value="UG">Uganda</option>
                            <option value="UA">Ukraine</option>
                            <option value="AE">United Arab Emirates</option>
                            <option value="GB">United Kingdom</option>
                            <option value="US" selected="selected">United States</option>
                            <option value="UM">United States Minor Outlying Islands</option>
                            <option value="UY">Uruguay</option>
                            <option value="UZ">Uzbekistan</option>
                            <option value="VU">Vanuatu</option>
                            <option value="VE">Venezuela</option>
                            <option value="VN">Vietnam</option>
                            <option value="VG">Virgin Islands, British</option>
                            <option value="VI">Virgin Islands, U.S.</option>
                            <option value="WF">Wallis and Futuna</option>
                            <option value="EH">Western Sahara</option>
                            <option value="YE">Yemen</option>
                            <option value="ZM">Zambia</option>
                            <option value="ZW">Zimbabwe</option>
                        </select>
                    </div>
                </div>

                <div class="input_outline">
                    <label for="">Customer Phone</label>
                    <p>*required for international orders</p>
                    <input type="tel" id="" name="" placeholder="Customer Phone">
                </div>
                <div class="input_outline">
                    <label for="">Packing Slip</label>
                    <p>If you don't have one not to worry, we can automatically generate one for you.<br>Visit <a href="/my-brand" target="_blank">My Brand</a> to update your preferences and set your logo and customer service information.</p>
                    <input class="mb-1" type="file" id="" name="">
                    <p><i>Allowed File Types: .pdf, .doc, .docx (1MB file limit)</i></p>
                </div>

                
                <table class="manual-orders-table table my-40">
                    <tr>
                        <th>Brand</th>
                        <th>Product Name/SKU</th>
                        <th>Color</th>
                        <th>Size</th>
                        <th>Quantity</th>
                        <th>Front Print</th>
                        <th>Front Mockup</th>
                        <th>Back Print</th>
                        <th>Back Mockup</th>
                        <th>Cost</th>
                    </tr>
                    <tr>
                        <td>
                            <select class="inventory_brand" name="BrandA" id="BrandA">
                                <option value="">Select</option><option value="6">Alternative</option>
                                <option value="2">American Apparel</option>
                                <option value="4">Anvil</option>
                                <option value="10">Augusta</option>
                                <option value="18">BAGedge</option>
                                <option value="22">Bayside</option>
                                <option value="5">Bella Canvas</option>
                                <option value="20">Big Accessories</option>
                                <option value="35">Champion</option>
                                <option value="38">Code V</option>
                                <option value="32">Econscious</option>
                                <option value="25">Epson Paper</option>
                                <option value="31">Fruit of the Loom</option>
                                <option value="1">Gildan</option>
                                <option value="8">Hanes</option>
                                <option value="24">Independent Trading</option>
                                <option value="39">LAT</option>
                                <option value="15">Liberty Bags</option>
                                <option value="17">Next Level</option>
                                <option value="27">Ryan Kikta Cases</option>
                                <option value="19">Ryan Kikta Drinkware</option>
                                <option value="29">Ryan Kikta Home</option>
                                <option value="3">Rabbit Skins</option>
                                <option value="33">Royal Apparel</option>
                                <option value="48">Tie Dye</option>
                                <option value="36">Ultra Club</option>
                            </select>
                        </td>
                        <td>
                            <select>
                                <option value="">Select</option>
                            </select>
                        </td>
                        <td>
                            <select>
                                <option value="">Select</option>
                            </select>
                        </td>
                        <td>
                            <select>
                                <option value="">Select</option>
                            </select>
                        </td>
                        <td>
                            <select>
                                <option value="">Select</option>
                            </select>
                        </td>
                        <td>
                            <select name="QuantityA" id="QuantityA">
                                <option value="1">1</option>
                                <option value="2">2</option>
                                <option value="3">3</option>
                                <option value="4">4</option>
                                <option value="5">5</option>
                                <option value="6">6</option>
                                <option value="7">7</option>
                                <option value="8">8</option>
                                <option value="9">9</option>
                                <option value="10">10</option>
                                <option value="11">11</option>
                                <option value="12">12</option>
                                <option value="13">13</option>
                                <option value="14">14</option>
                                <option value="15">15</option>
                                <option value="16">16</option>
                                <option value="17">17</option>
                                <option value="18">18</option>
                                <option value="19">19</option>
                                <option value="20">20</option>
                                <option value="21">21</option>
                                <option value="22">22</option>
                                <option value="23">23</option>
                                <option value="24">24</option>
                                <option value="25">25</option>
                                <option value="26">26</option>
                                <option value="27">27</option>
                                <option value="28">28</option>
                                <option value="29">29</option>
                                <option value="30">30</option>
                                <option value="31">31</option>
                                <option value="32">32</option>
                                <option value="33">33</option>
                                <option value="34">34</option>
                                <option value="35">35</option>
                                <option value="36">36</option>
                                <option value="37">37</option>
                                <option value="38">38</option>
                                <option value="39">39</option>
                                <option value="40">40</option>
                                <option value="41">41</option>
                                <option value="42">42</option>
                                <option value="43">43</option>
                                <option value="44">44</option>
                                <option value="45">45</option>
                                <option value="46">46</option>
                                <option value="47">47</option>
                                <option value="48">48</option>
                                <option value="49">49</option>
                                <option value="50">50</option>
                                <option value="51">51</option>
                                <option value="52">52</option>
                                <option value="53">53</option>
                                <option value="54">54</option>
                                <option value="55">55</option>
                                <option value="56">56</option>
                                <option value="57">57</option>
                                <option value="58">58</option>
                                <option value="59">59</option>
                                <option value="60">60</option>
                                <option value="61">61</option>
                                <option value="62">62</option>
                                <option value="63">63</option>
                                <option value="64">64</option>
                                <option value="65">65</option>
                                <option value="66">66</option>
                                <option value="67">67</option>
                                <option value="68">68</option>
                                <option value="69">69</option>
                                <option value="70">70</option>
                                <option value="71">71</option>
                                <option value="72">72</option>
                                <option value="73">73</option>
                                <option value="74">74</option>
                                <option value="75">75</option>
                                <option value="76">76</option>
                                <option value="77">77</option>
                                <option value="78">78</option>
                                <option value="79">79</option>
                                <option value="80">80</option>
                                <option value="81">81</option>
                                <option value="82">82</option>
                                <option value="83">83</option>
                                <option value="84">84</option>
                                <option value="85">85</option>
                                <option value="86">86</option>
                                <option value="87">87</option>
                                <option value="88">88</option>
                                <option value="89">89</option>
                                <option value="90">90</option>
                                <option value="91">91</option>
                                <option value="92">92</option>
                                <option value="93">93</option>
                                <option value="94">94</option>
                                <option value="95">95</option>
                                <option value="96">96</option>
                                <option value="97">97</option>
                                <option value="98">98</option>
                                <option value="99">99</option>
                                <option value="100">100</option>
                            </select>
                        </td>
                        <td>
                            <p>Not Selected</p>
                            <p><a href='#'>Select</a></p>
                        </td>
                        <td>
                            <p>Not Selected</p>
                            <p><a href='#'>Select</a></p>
                        </td>
                        <td>
                            <p>Not Selected</p>
                            <p><a href='#'>Select</a></p>
                        </td>
                        <td>
                            <div class="input_outline">
                                <input type="text" value="0.00">
                            </div>
                        </td>
                    </tr>
                </table>
                <div class="d-flex justify-content-end mb-4">
                    <a href="#">Add Another Item</a>
                </div>

                <div>
                    <label for="">Shipping Method*</label><label class="small_label">Shipping Cost: $0.00</label>
                    <select class="inventory_size" name="ShippingMethod" id="ShippingMethod" style="width:auto;display: inline">
                        <option value="1">Standard Shipping (United States)</option>
                    </select>
                </div>

                <label for="">Neck Brand Label Removal? ($0.50 each)</label>
                <p>Only the brand label will be removed. The other tag is legally required unless it is replaced with your own label that meets the legal guidelines for apparel labeling. Read more about our label service<a href="/my-brand" target="_blank">label service</a>.</p>
                <select>
                    <option value="">No</option>
                    <option value="">Yes</option>
                </select>


                <label for="">Individual Bagging ($1.00 each)</label>
                <p>Each shirt will be folded in its own clear polybag with a size sticker on the outside.</p>
                <select>
                    <option value="">No</option>
                    <option value="">Yes</option>
                </select>

                <label for="">Rush Order ($2.00 each)</label>
                <p>Rush processing is typically 2-3 business days instead of 4-5 business days. Daily cutoff is 12:30ET.</p>
                <select>
                    <option value="">No</option>
                    <option value="">Yes</option>
                </select>

            </form>
        </div>
    </div> <!-- content-wrapper -->
</div>
<?php get_footer();?>

