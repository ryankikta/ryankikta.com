<?php
// Only show if logged in
$current_user = wp_get_current_user();
if (0 == $current_user->ID) {
    wp_redirect("/login");
    exit();
}

get_header();
?>

<div class="container-fluid">
	<div class="row">
		<div class="col">

	<!-- This is what's rendered from display_functions.php -->
	<script language="javascript" src="/wp-content/plugins/rmproductmanagement/javascript/Ajaxfileupload-jquery-1.3.2.js"></script>
	<script language="javascript" src="/wp-content/plugins/rmproductmanagement/javascript/ajaxupload.3.5.js"></script>
	<script language="javascript" src="/wp-content/plugins/rmproductmanagement/javascript/inventory.js"></script>
	<div id="wpcf7-f320-p322-o1" class="wpcf7">
	<form enctype="multipart/form-data" class="wpcf7-form invalid" method="post" action="/submitorder/" name="rmpmForm" id="rmpmForm">
				<div style="display: none;">
				<input type="hidden" value="320" name="_wpcf7"><br>
				<input type="hidden" value="3.2.1" name="_wpcf7_version"><br>
				<input type="hidden" value="wpcf7-f320-p322-o1" name="_wpcf7_unit_tag"><br>
				<input type="hidden" value="3e4c7ed2bf" name="_wpnonce">
				<input type="hidden" value="" name="order_total" id="order_total">
				<input type="hidden" value="/wp-content" name="siteContentURL" id="siteContentURL">
				</div>
				<div id="add_success" class="add_success" style="display:none"> Your Order Submited Successfully </div>
				<h2>Your Information</h2>
				<p>Business Name (required)<br>
				<span class="wpcf7-form-control-wrap businessname"><input type="text" size="40" class="wpcf7-form-control wpcf7-text wpcf7-validates-as-required" value="Solution Agency" name="businessname" id="businessname" style="background-image: url(&quot;data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACAAAAAgCAYAAABzenr0AAAAAXNSR0IArs4c6QAAAfBJREFUWAntVk1OwkAUZkoDKza4Utm61iP0AqyIDXahN2BjwiHYGU+gizap4QDuegWN7lyCbMSlCQjU7yO0TOlAi6GwgJc0fT/fzPfmzet0crmD7HsFBAvQbrcrw+Gw5fu+AfOYvgylJ4TwCoVCs1ardYTruqfj8fgV5OUMSVVT93VdP9dAzpVvm5wJHZFbg2LQ2pEYOlZ/oiDvwNcsFoseY4PBwMCrhaeCJyKWZU37KOJcYdi27QdhcuuBIb073BvTNL8ln4NeeR6NRi/wxZKQcGurQs5oNhqLshzVTMBewW/LMU3TTNlO0ieTiStjYhUIyi6DAp0xbEdgTt+LE0aCKQw24U4llsCs4ZRJrYopB6RwqnpA1YQ5NGFZ1YQ41Z5S8IQQdP5laEBRJcD4Vj5DEsW2gE6s6g3d/YP/g+BDnT7GNi2qCjTwGd6riBzHaaCEd3Js01vwCPIbmWBRx1nwAN/1ov+/drgFWIlfKpVukyYihtgkXNp4mABK+1GtVr+SBhJDbBIubVw+Cd/TDgKO2DPiN3YUo6y/nDCNEIsqTKH1en2tcwA9FKEItyDi3aIh8Gl1sRrVnSDzNFDJT1bAy5xpOYGn5fP5JuL95ZjMIn1ya7j5dPGfv0A5eAnpZUY3n5jXcoec5J67D9q+VuAPM47D3XaSeL4AAAAASUVORK5CYII=&quot;); background-repeat: no-repeat; background-attachment: scroll; background-size: 16px 18px; background-position: 98% 50%;"></span><span id="businessname_err" style="color:red"></span>  </p>
				<p>Business Contact Name (required)<br>
					<span class="wpcf7-form-control-wrap businesscontact"><input type="text" size="40" class="wpcf7-form-control wpcf7-text wpcf7-validates-as-required wpcf7-not-valid" value="" name="businesscontact"></span><span id="businesscontact_err" style="color:red"></span> </p>
				<p>Your Email (required)<br>
					<span class="wpcf7-form-control-wrap youremail"><input type="text" size="40" class="wpcf7-form-control wpcf7-text wpcf7-email wpcf7-validates-as-required wpcf7-validates-as-email" value="aaron@solutionagency.net" name="youremail"></span> <span id="youremail_err" style="color:red"></span></p>
				<h4>Return Label</h4>
                                <span>You can change the return label from <a href="/my-brand/">here</a><br>
				Solution Agency<br>
				2 Wurz Avenue<br>Yorkville, NY 13495 </span>
                                  <br><br> 
				<!--<p>Please enter as follows:<br>
				Business Name<br>
				Address 1<br>
				Address 2<br>
				City, State/Province ZipCode </p>
				<p>This will display on the outside of the package on the shipping label. You are welcome to use your business name above with our mailing address like "Your Brand Name" 2 Wurz Avenue. Yorkville, NY 13495 but please make sure to enter yours or our address completely.<br>
					<span class="wpcf7-form-control-wrap returnlabel"><textarea rows="10" cols="40" class="wpcf7-form-control  wpcf7-textarea wpcf7-validates-as-required wpcf7-not-valid" name="returnlabel"></textarea></span> </p>
				-->
                                <h2>Order Details</h2>
				<p>Order ID (required)<br>
                                 <span style="font-style:italic;font-weight:normal">The Order ID is for your reference and will appear on your order packing slip. Enter whatever you want.</span>
					  <span class="wpcf7-form-control-wrap orderid"><input type="text" size="40" class="wpcf7-form-control wpcf7-text wpcf7-validates-as-required wpcf7-not-valid" value="" name="orderid"></span> <span id="orderid_err" style="color:red"></span> </p>
                                          
                        	<p>Sample Voucher
                                 <span style="font-style:italic;font-weight:normal"><a href="/samples/" target="_blank"> View More Information</a></span>
       		                 <span class="wpcf7-form-control-wrap discount"><input type="text" size="40" class="wpcf7-form-control wpcf7-text wpcf7-not-valid" value="" name="discount"></span> <span id="discount_err" style="color:red"></span> </p>
                                
                                 
				<h4>Shipping Address (required)</h4>
				<!--<p>Please enter as follows:<br>
				FirstName LastName<br>
				Address 1<br>
				Address 2<br>
				City, State/Province ZipCode </p>
				<p>    <span class="wpcf7-form-control-wrap shippingaddress"><textarea rows="10" cols="40" class="wpcf7-form-control  wpcf7-textarea wpcf7-validates-as-required wpcf7-not-valid" name="shippingaddress"></textarea></span> <span id="shippingaddress_err" style="color:red"></span></p>
				-->
                                <p>Customer Name (required)<br>
					  <span class="wpcf7-form-control-wrap clientname"><input type="text" size="40" class="wpcf7-form-control wpcf7-text wpcf7-validates-as-required wpcf7-not-valid" value="" name="clientname"></span> <span id="clientname_err" style="color:red"></span> </p>
                                
                                <p>Address1 (required)<br>
					  <span class="wpcf7-form-control-wrap address1"><input type="text" size="40" class="wpcf7-form-control wpcf7-text wpcf7-validates-as-required wpcf7-not-valid" value="" name="address1"></span> <span id="address1_err" style="color:red"></span> </p>
                                <p>Address2 (optional)<br>
					  <span class="wpcf7-form-control-wrap address2"><input type="text" size="40" class="wpcf7-form-control wpcf7-text  wpcf7-not-valid" value="" name="address2"></span> <span id="address2_err" style="color:red"></span> </p>
                                <p>City (required)<br>
					  <span class="wpcf7-form-control-wrap city"><input type="text" size="40" class="wpcf7-form-control wpcf7-text wpcf7-validates-as-required wpcf7-not-valid" value="" name="city"></span> <span id="city_err" style="color:red"></span> </p>
                                <p>State/Province (required)<br>
					  <span class="wpcf7-form-control-wrap state"><input type="text" size="40" class="wpcf7-form-control wpcf7-text wpcf7-validates-as-required wpcf7-not-valid" value="" name="state"></span> <span id="state_err" style="color:red"></span> </p>
                                <p>Zip Code (required)<br>
					  <span class="wpcf7-form-control-wrap zipcode"><input type="text" size="40" class="wpcf7-form-control wpcf7-text wpcf7-validates-as-required wpcf7-not-valid" value="" name="zipcode"></span> <span id="zipcode_err" style="color:red"></span> </p>
                                <p>Country (required)<br>
					  <span class="wpcf7-form-control-wrap country">
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
                                          </span>  </p>
				
                                <!--<p>Shipping Method (required)<br>
				<span class="wpcf7-form-control-wrap ShippingMethod"><br><select  class="inventory_size" name="ShippingMethod" id="ShippingMethod" style="width:auto;display: inline">
<option  value="1">Standard Shipping (United States)</option>
<option  value="2">Standard Shipping (Canada)</option>
<option  value="3">Standard Shipping (International)</option>
</select></span></p><p></p>-->
				<p>Customer Phone (required only for International orders) <br>
					<span class="wpcf7-form-control-wrap customerphone"><input type="text" size="40" class="wpcf7-form-control wpcf7-text" value="" name="customerphone"></span> </p>
				<p style="font-weight:normal;"><strong>Packing Slip (optional)</strong><br>
				<span style="font-style:italic">Allowed file types: pdf, doc, docx. (1MB file limit)</span><br>
                                If you don't have one not to worry,  we can automatically generate one for you. Visit<a href="/my-brand/" target="_blank"> My Brand</a> to update<br> your preferences and set your logo and customer service information.<br> 
				<span class="wpcf7-form-control-wrap PackingSlip"><input type="text" value="" size="40" class="wpcf7-form-control  wpcf7-file" name="PackingSlip" id="PackingSlip" readonly=""> </span><input type="button" id="PackingSlip_btn" value="Browse"></p>
				<p>Order details (required) For EACH order please specify: </p>
<p><span id="brand_err_info" style="color:red"></span><span id="brand_err" style="color:red"></span></p>
				<table width="750" border="0" class="wpcf7-order">
				<tbody><tr class="head">
				<td>Brand</td>
				<td>Product Name/ SKU</td>
				<td>Color</td>
				<td>Size</td>
				<td>Quantity</td>
				<td>Front Print</td>
				<td>Front Mockup</td>
				<td>Back Print</td>
				<td>Back Mockup</td>
				<td>Cost</td>
				</tr>
				<tr id="holderA">
				<td><span class="wpcf7-form-control-wrap"><select class="inventory_brand" name="BrandA" id="BrandA" style="width:100px;">
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
<input name="shipping_typeA" id="shipping_typeA" style="display:none;"></span></td><td><span class="wpcf7-form-control-wrap"><input type="hidden" value="1" id="rushedA"><select name="NameA" id="NameA" style="width:100px;"><option value="">Select</option></select></span></td><td><span class="wpcf7-form-control-wrap"><br><select name="ColorA" id="ColorA" style="width:90px;"><option value="">Select</option></select></span></td><td><span class="wpcf7-form-control-wrap"><br><select name="SizeA" id="SizeA" style="width:90px;"><option value="">Select</option></select></span></td><td><span class="wpcf7-form-control-wrap"><br><select name="QuantityA" id="QuantityA" style="width:50px;"><option value="1">1</option><option value="2">2</option><option value="3">3</option><option value="4">4</option><option value="5">5</option><option value="6">6</option><option value="7">7</option><option value="8">8</option><option value="9">9</option><option value="10">10</option><option value="11">11</option><option value="12">12</option><option value="13">13</option><option value="14">14</option><option value="15">15</option><option value="16">16</option><option value="17">17</option><option value="18">18</option><option value="19">19</option><option value="20">20</option><option value="21">21</option><option value="22">22</option><option value="23">23</option><option value="24">24</option><option value="25">25</option><option value="26">26</option><option value="27">27</option><option value="28">28</option><option value="29">29</option><option value="30">30</option><option value="31">31</option><option value="32">32</option><option value="33">33</option><option value="34">34</option><option value="35">35</option><option value="36">36</option><option value="37">37</option><option value="38">38</option><option value="39">39</option><option value="40">40</option><option value="41">41</option><option value="42">42</option><option value="43">43</option><option value="44">44</option><option value="45">45</option><option value="46">46</option><option value="47">47</option><option value="48">48</option><option value="49">49</option><option value="50">50</option><option value="51">51</option><option value="52">52</option><option value="53">53</option><option value="54">54</option><option value="55">55</option><option value="56">56</option><option value="57">57</option><option value="58">58</option><option value="59">59</option><option value="60">60</option><option value="61">61</option><option value="62">62</option><option value="63">63</option><option value="64">64</option><option value="65">65</option><option value="66">66</option><option value="67">67</option><option value="68">68</option><option value="69">69</option><option value="70">70</option><option value="71">71</option><option value="72">72</option><option value="73">73</option><option value="74">74</option><option value="75">75</option><option value="76">76</option><option value="77">77</option><option value="78">78</option><option value="79">79</option><option value="80">80</option><option value="81">81</option><option value="82">82</option><option value="83">83</option><option value="84">84</option><option value="85">85</option><option value="86">86</option><option value="87">87</option><option value="88">88</option><option value="89">89</option><option value="90">90</option><option value="91">91</option><option value="92">92</option><option value="93">93</option><option value="94">94</option><option value="95">95</option><option value="96">96</option><option value="97">97</option><option value="98">98</option><option value="99">99</option><option value="100">100</option></select></span></td>
				<td><span class="wpcf7-form-control-wrap">
				<span style="position: relative; bottom: 7px;">
				<div style="width: 100px;overflow: hidden;" id="frontPrintAtext">Not Selected</div>
				<a class="dialog" onclick="SetVar('frontPrintA');" href="#frontPrintA">Select</a>
				<span style="display: none;" id="frontPrintAremove">- <a onclick="remove1('frontPrintA','A');" href="#frontPrintA">Remove</a></span>
				<input type="hidden" name="frontPrintA" id="frontPrintA">
                                    <input value="0" type="hidden" name="frontJumboA" id="frontJumboA">
                                        <input value="0" type="hidden" name="frontUnderbaseA" id="frontUnderbaseA">
                                    
				</span>
				</span></td>
				<td><span class="wpcf7-form-control-wrap">
				<span style="position: relative; bottom: 7px;">
				<div style="width: 100px;overflow: hidden;" id="frontMockupAtext">Not Selected</div>
				<a class="dialog" onclick="SetVar('frontMockupA');" href="#frontMockupA">Select</a>
				<span style="display: none;" id="frontMockupAremove">- <a onclick="remove1('frontMockupA','A');" href="#frontMockupA">Remove</a></span>
				<input type="hidden" name="frontMockupA" id="frontMockupA">
				</span></span></td>
				<td><span class="wpcf7-form-control-wrap" id="BackPrintCellA">
				<span style="position: relative; bottom: 7px;">
				<div style="width: 100px;overflow: hidden;" id="backPrintAtext">Not Selected</div>
				<a class="dialog" onclick="SetVar('backPrintA');" href="#backPrintA">Select</a>
				<span style="display: none;" id="backPrintAremove">- <a onclick="remove1('backPrintA','A');" href="#backPrintA">Remove</a></span>
				<input type="hidden" name="backPrintA" id="backPrintA">
                                <input value="0" type="hidden" name="backJumboA" id="backJumboA">
                                    <input value="0" type="hidden" name="backUnderbaseA" id="backUnderbaseA">
			 	</span></span></td>
				<td><span class="wpcf7-form-control-wrap" id="BackMockupCellA">
				<span style="position: relative; bottom: 7px;">
				<div style="width: 100px;overflow: hidden;" id="backMockupAtext">Not Selected</div>
				<a class="dialog" onclick="SetVar('backMockupA');" href="#backMockupA">Select</a>
				<span style="display: none;" id="backMockupAremove">- <a onclick="remove1('backMockupA','A');" href="#backMockupA">Remove</a></span>
				<input type="hidden" name="backMockupA" id="backMockupA">
                                    
			 </span> </span></td>
				<td><input type="text" style="width:50px;" name="CostA" value="0.00" id="CostA" readonly="">
				<input type="hidden" name="PrintPriceA" value="0" id="PrintPriceA">
                                    
				</td>
				</tr>
				<tr id="holderB">
				<td><span class="wpcf7-form-control-wrap"><select class="inventory_brand" name="BrandB" id="BrandB" style="width:100px;">
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
<input name="shipping_typeB" id="shipping_typeB" style="display:none;"></span></td><td><span class="wpcf7-form-control-wrap"><input type="hidden" value="1" id="rushedB"><select name="NameB" id="NameB" style="width:100px;"><option value="">Select</option></select></span></td><td><span class="wpcf7-form-control-wrap"><br><select name="ColorB" id="ColorB" style="width:90px;"><option value="">Select</option></select></span></td><td><span class="wpcf7-form-control-wrap"><br><select name="SizeB" id="SizeB" style="width:90px;"><option value="">Select</option></select></span></td><td><span class="wpcf7-form-control-wrap"><br><select name="QuantityB" id="QuantityB" style="width:50px;"><option value="1">1</option><option value="2">2</option><option value="3">3</option><option value="4">4</option><option value="5">5</option><option value="6">6</option><option value="7">7</option><option value="8">8</option><option value="9">9</option><option value="10">10</option><option value="11">11</option><option value="12">12</option><option value="13">13</option><option value="14">14</option><option value="15">15</option><option value="16">16</option><option value="17">17</option><option value="18">18</option><option value="19">19</option><option value="20">20</option><option value="21">21</option><option value="22">22</option><option value="23">23</option><option value="24">24</option><option value="25">25</option><option value="26">26</option><option value="27">27</option><option value="28">28</option><option value="29">29</option><option value="30">30</option><option value="31">31</option><option value="32">32</option><option value="33">33</option><option value="34">34</option><option value="35">35</option><option value="36">36</option><option value="37">37</option><option value="38">38</option><option value="39">39</option><option value="40">40</option><option value="41">41</option><option value="42">42</option><option value="43">43</option><option value="44">44</option><option value="45">45</option><option value="46">46</option><option value="47">47</option><option value="48">48</option><option value="49">49</option><option value="50">50</option><option value="51">51</option><option value="52">52</option><option value="53">53</option><option value="54">54</option><option value="55">55</option><option value="56">56</option><option value="57">57</option><option value="58">58</option><option value="59">59</option><option value="60">60</option><option value="61">61</option><option value="62">62</option><option value="63">63</option><option value="64">64</option><option value="65">65</option><option value="66">66</option><option value="67">67</option><option value="68">68</option><option value="69">69</option><option value="70">70</option><option value="71">71</option><option value="72">72</option><option value="73">73</option><option value="74">74</option><option value="75">75</option><option value="76">76</option><option value="77">77</option><option value="78">78</option><option value="79">79</option><option value="80">80</option><option value="81">81</option><option value="82">82</option><option value="83">83</option><option value="84">84</option><option value="85">85</option><option value="86">86</option><option value="87">87</option><option value="88">88</option><option value="89">89</option><option value="90">90</option><option value="91">91</option><option value="92">92</option><option value="93">93</option><option value="94">94</option><option value="95">95</option><option value="96">96</option><option value="97">97</option><option value="98">98</option><option value="99">99</option><option value="100">100</option></select></span></td>
				<td><span class="wpcf7-form-control-wrap">
				<span style="position: relative; bottom: 7px;">
				<div style="width: 100px;overflow: hidden;" id="frontPrintBtext">Not Selected</div>
				<a class="dialog" onclick="SetVar('frontPrintB');" href="#frontPrintB">Select</a>
				<span style="display: none;" id="frontPrintBremove">- <a onclick="remove1('frontPrintB','B');" href="#frontPrintB">Remove</a></span>
				<input type="hidden" name="frontPrintB" id="frontPrintB">
                                    <input value="0" type="hidden" name="frontJumboB" id="frontJumboB">
                                        <input value="0" type="hidden" name="frontUnderbaseB" id="frontUnderbaseB">
                                    
				</span>
				</span></td>
				<td><span class="wpcf7-form-control-wrap">
				<span style="position: relative; bottom: 7px;">
				<div style="width: 100px;overflow: hidden;" id="frontMockupBtext">Not Selected</div>
				<a class="dialog" onclick="SetVar('frontMockupB');" href="#frontMockupB">Select</a>
				<span style="display: none;" id="frontMockupBremove">- <a onclick="remove1('frontMockupB','B');" href="#frontMockupB">Remove</a></span>
				<input type="hidden" name="frontMockupB" id="frontMockupB">
				</span></span></td>
				<td><span class="wpcf7-form-control-wrap" id="BackPrintCellB">
				<span style="position: relative; bottom: 7px;">
				<div style="width: 100px;overflow: hidden;" id="backPrintBtext">Not Selected</div>
				<a class="dialog" onclick="SetVar('backPrintB');" href="#backPrintB">Select</a>
				<span style="display: none;" id="backPrintBremove">- <a onclick="remove1('backPrintB','B');" href="#backPrintB">Remove</a></span>
				<input type="hidden" name="backPrintB" id="backPrintB">
                                <input value="0" type="hidden" name="backJumboB" id="backJumboB">
                                    <input value="0" type="hidden" name="backUnderbaseB" id="backUnderbaseB">
			 	</span></span></td>
				<td><span class="wpcf7-form-control-wrap" id="BackMockupCellB">
				<span style="position: relative; bottom: 7px;">
				<div style="width: 100px;overflow: hidden;" id="backMockupBtext">Not Selected</div>
				<a class="dialog" onclick="SetVar('backMockupB');" href="#backMockupB">Select</a>
				<span style="display: none;" id="backMockupBremove">- <a onclick="remove1('backMockupB','B');" href="#backMockupB">Remove</a></span>
				<input type="hidden" name="backMockupB" id="backMockupB">
                                    
			 </span> </span></td>
				<td><input type="text" style="width:50px;" name="CostB" value="0.00" id="CostB" readonly="">
				<input type="hidden" name="PrintPriceB" value="0" id="PrintPriceB">
                                    
				</td>
				</tr>
				<tr id="holderC">
				<td><span class="wpcf7-form-control-wrap"><select class="inventory_brand" name="BrandC" id="BrandC" style="width:100px;">
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
<input name="shipping_typeC" id="shipping_typeC" style="display:none;"></span></td><td><span class="wpcf7-form-control-wrap"><input type="hidden" value="1" id="rushedC"><select name="NameC" id="NameC" style="width:100px;"><option value="">Select</option></select></span></td><td><span class="wpcf7-form-control-wrap"><br><select name="ColorC" id="ColorC" style="width:90px;"><option value="">Select</option></select></span></td><td><span class="wpcf7-form-control-wrap"><br><select name="SizeC" id="SizeC" style="width:90px;"><option value="">Select</option></select></span></td><td><span class="wpcf7-form-control-wrap"><br><select name="QuantityC" id="QuantityC" style="width:50px;"><option value="1">1</option><option value="2">2</option><option value="3">3</option><option value="4">4</option><option value="5">5</option><option value="6">6</option><option value="7">7</option><option value="8">8</option><option value="9">9</option><option value="10">10</option><option value="11">11</option><option value="12">12</option><option value="13">13</option><option value="14">14</option><option value="15">15</option><option value="16">16</option><option value="17">17</option><option value="18">18</option><option value="19">19</option><option value="20">20</option><option value="21">21</option><option value="22">22</option><option value="23">23</option><option value="24">24</option><option value="25">25</option><option value="26">26</option><option value="27">27</option><option value="28">28</option><option value="29">29</option><option value="30">30</option><option value="31">31</option><option value="32">32</option><option value="33">33</option><option value="34">34</option><option value="35">35</option><option value="36">36</option><option value="37">37</option><option value="38">38</option><option value="39">39</option><option value="40">40</option><option value="41">41</option><option value="42">42</option><option value="43">43</option><option value="44">44</option><option value="45">45</option><option value="46">46</option><option value="47">47</option><option value="48">48</option><option value="49">49</option><option value="50">50</option><option value="51">51</option><option value="52">52</option><option value="53">53</option><option value="54">54</option><option value="55">55</option><option value="56">56</option><option value="57">57</option><option value="58">58</option><option value="59">59</option><option value="60">60</option><option value="61">61</option><option value="62">62</option><option value="63">63</option><option value="64">64</option><option value="65">65</option><option value="66">66</option><option value="67">67</option><option value="68">68</option><option value="69">69</option><option value="70">70</option><option value="71">71</option><option value="72">72</option><option value="73">73</option><option value="74">74</option><option value="75">75</option><option value="76">76</option><option value="77">77</option><option value="78">78</option><option value="79">79</option><option value="80">80</option><option value="81">81</option><option value="82">82</option><option value="83">83</option><option value="84">84</option><option value="85">85</option><option value="86">86</option><option value="87">87</option><option value="88">88</option><option value="89">89</option><option value="90">90</option><option value="91">91</option><option value="92">92</option><option value="93">93</option><option value="94">94</option><option value="95">95</option><option value="96">96</option><option value="97">97</option><option value="98">98</option><option value="99">99</option><option value="100">100</option></select></span></td>
				<td><span class="wpcf7-form-control-wrap">
				<span style="position: relative; bottom: 7px;">
				<div style="width: 100px;overflow: hidden;" id="frontPrintCtext">Not Selected</div>
				<a class="dialog" onclick="SetVar('frontPrintC');" href="#frontPrintC">Select</a>
				<span style="display: none;" id="frontPrintCremove">- <a onclick="remove1('frontPrintC','C');" href="#frontPrintC">Remove</a></span>
				<input type="hidden" name="frontPrintC" id="frontPrintC">
                                    <input value="0" type="hidden" name="frontJumboC" id="frontJumboC">
                                        <input value="0" type="hidden" name="frontUnderbaseC" id="frontUnderbaseC">
                                    
				</span>
				</span></td>
				<td><span class="wpcf7-form-control-wrap">
				<span style="position: relative; bottom: 7px;">
				<div style="width: 100px;overflow: hidden;" id="frontMockupCtext">Not Selected</div>
				<a class="dialog" onclick="SetVar('frontMockupC');" href="#frontMockupC">Select</a>
				<span style="display: none;" id="frontMockupCremove">- <a onclick="remove1('frontMockupC','C');" href="#frontMockupC">Remove</a></span>
				<input type="hidden" name="frontMockupC" id="frontMockupC">
				</span></span></td>
				<td><span class="wpcf7-form-control-wrap" id="BackPrintCellC">
				<span style="position: relative; bottom: 7px;">
				<div style="width: 100px;overflow: hidden;" id="backPrintCtext">Not Selected</div>
				<a class="dialog" onclick="SetVar('backPrintC');" href="#backPrintC">Select</a>
				<span style="display: none;" id="backPrintCremove">- <a onclick="remove1('backPrintC','C');" href="#backPrintC">Remove</a></span>
				<input type="hidden" name="backPrintC" id="backPrintC">
                                <input value="0" type="hidden" name="backJumboC" id="backJumboC">
                                    <input value="0" type="hidden" name="backUnderbaseC" id="backUnderbaseC">
			 	</span></span></td>
				<td><span class="wpcf7-form-control-wrap" id="BackMockupCellC">
				<span style="position: relative; bottom: 7px;">
				<div style="width: 100px;overflow: hidden;" id="backMockupCtext">Not Selected</div>
				<a class="dialog" onclick="SetVar('backMockupC');" href="#backMockupC">Select</a>
				<span style="display: none;" id="backMockupCremove">- <a onclick="remove1('backMockupC','C');" href="#backMockupC">Remove</a></span>
				<input type="hidden" name="backMockupC" id="backMockupC">
                                    
			 </span> </span></td>
				<td><input type="text" style="width:50px;" name="CostC" value="0.00" id="CostC" readonly="">
				<input type="hidden" name="PrintPriceC" value="0" id="PrintPriceC">
                                    
				</td>
				</tr>
				<tr id="holderD">
				<td><span class="wpcf7-form-control-wrap"><select class="inventory_brand" name="BrandD" id="BrandD" style="width:100px;">
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
<input name="shipping_typeD" id="shipping_typeD" style="display:none;"></span></td><td><span class="wpcf7-form-control-wrap"><input type="hidden" value="1" id="rushedD"><select name="NameD" id="NameD" style="width:100px;"><option value="">Select</option></select></span></td><td><span class="wpcf7-form-control-wrap"><br><select name="ColorD" id="ColorD" style="width:90px;"><option value="">Select</option></select></span></td><td><span class="wpcf7-form-control-wrap"><br><select name="SizeD" id="SizeD" style="width:90px;"><option value="">Select</option></select></span></td><td><span class="wpcf7-form-control-wrap"><br><select name="QuantityD" id="QuantityD" style="width:50px;"><option value="1">1</option><option value="2">2</option><option value="3">3</option><option value="4">4</option><option value="5">5</option><option value="6">6</option><option value="7">7</option><option value="8">8</option><option value="9">9</option><option value="10">10</option><option value="11">11</option><option value="12">12</option><option value="13">13</option><option value="14">14</option><option value="15">15</option><option value="16">16</option><option value="17">17</option><option value="18">18</option><option value="19">19</option><option value="20">20</option><option value="21">21</option><option value="22">22</option><option value="23">23</option><option value="24">24</option><option value="25">25</option><option value="26">26</option><option value="27">27</option><option value="28">28</option><option value="29">29</option><option value="30">30</option><option value="31">31</option><option value="32">32</option><option value="33">33</option><option value="34">34</option><option value="35">35</option><option value="36">36</option><option value="37">37</option><option value="38">38</option><option value="39">39</option><option value="40">40</option><option value="41">41</option><option value="42">42</option><option value="43">43</option><option value="44">44</option><option value="45">45</option><option value="46">46</option><option value="47">47</option><option value="48">48</option><option value="49">49</option><option value="50">50</option><option value="51">51</option><option value="52">52</option><option value="53">53</option><option value="54">54</option><option value="55">55</option><option value="56">56</option><option value="57">57</option><option value="58">58</option><option value="59">59</option><option value="60">60</option><option value="61">61</option><option value="62">62</option><option value="63">63</option><option value="64">64</option><option value="65">65</option><option value="66">66</option><option value="67">67</option><option value="68">68</option><option value="69">69</option><option value="70">70</option><option value="71">71</option><option value="72">72</option><option value="73">73</option><option value="74">74</option><option value="75">75</option><option value="76">76</option><option value="77">77</option><option value="78">78</option><option value="79">79</option><option value="80">80</option><option value="81">81</option><option value="82">82</option><option value="83">83</option><option value="84">84</option><option value="85">85</option><option value="86">86</option><option value="87">87</option><option value="88">88</option><option value="89">89</option><option value="90">90</option><option value="91">91</option><option value="92">92</option><option value="93">93</option><option value="94">94</option><option value="95">95</option><option value="96">96</option><option value="97">97</option><option value="98">98</option><option value="99">99</option><option value="100">100</option></select></span></td>
				<td><span class="wpcf7-form-control-wrap">
				<span style="position: relative; bottom: 7px;">
				<div style="width: 100px;overflow: hidden;" id="frontPrintDtext">Not Selected</div>
				<a class="dialog" onclick="SetVar('frontPrintD');" href="#frontPrintD">Select</a>
				<span style="display: none;" id="frontPrintDremove">- <a onclick="remove1('frontPrintD','D');" href="#frontPrintD">Remove</a></span>
				<input type="hidden" name="frontPrintD" id="frontPrintD">
                                    <input value="0" type="hidden" name="frontJumboD" id="frontJumboD">
                                        <input value="0" type="hidden" name="frontUnderbaseD" id="frontUnderbaseD">
                                    
				</span>
				</span></td>
				<td><span class="wpcf7-form-control-wrap">
				<span style="position: relative; bottom: 7px;">
				<div style="width: 100px;overflow: hidden;" id="frontMockupDtext">Not Selected</div>
				<a class="dialog" onclick="SetVar('frontMockupD');" href="#frontMockupD">Select</a>
				<span style="display: none;" id="frontMockupDremove">- <a onclick="remove1('frontMockupD','D');" href="#frontMockupD">Remove</a></span>
				<input type="hidden" name="frontMockupD" id="frontMockupD">
				</span></span></td>
				<td><span class="wpcf7-form-control-wrap" id="BackPrintCellD">
				<span style="position: relative; bottom: 7px;">
				<div style="width: 100px;overflow: hidden;" id="backPrintDtext">Not Selected</div>
				<a class="dialog" onclick="SetVar('backPrintD');" href="#backPrintD">Select</a>
				<span style="display: none;" id="backPrintDremove">- <a onclick="remove1('backPrintD','D');" href="#backPrintD">Remove</a></span>
				<input type="hidden" name="backPrintD" id="backPrintD">
                                <input value="0" type="hidden" name="backJumboD" id="backJumboD">
                                    <input value="0" type="hidden" name="backUnderbaseD" id="backUnderbaseD">
			 	</span></span></td>
				<td><span class="wpcf7-form-control-wrap" id="BackMockupCellD">
				<span style="position: relative; bottom: 7px;">
				<div style="width: 100px;overflow: hidden;" id="backMockupDtext">Not Selected</div>
				<a class="dialog" onclick="SetVar('backMockupD');" href="#backMockupD">Select</a>
				<span style="display: none;" id="backMockupDremove">- <a onclick="remove1('backMockupD','D');" href="#backMockupD">Remove</a></span>
				<input type="hidden" name="backMockupD" id="backMockupD">
                                    
			 </span> </span></td>
				<td><input type="text" style="width:50px;" name="CostD" value="0.00" id="CostD" readonly="">
				<input type="hidden" name="PrintPriceD" value="0" id="PrintPriceD">
                                    
				</td>
				</tr>
				<tr id="holderE">
				<td><span class="wpcf7-form-control-wrap"><select class="inventory_brand" name="BrandE" id="BrandE" style="width:100px;">
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
<input name="shipping_typeE" id="shipping_typeE" style="display:none;"></span></td><td><span class="wpcf7-form-control-wrap"><input type="hidden" value="1" id="rushedE"><select name="NameE" id="NameE" style="width:100px;"><option value="">Select</option></select></span></td><td><span class="wpcf7-form-control-wrap"><br><select name="ColorE" id="ColorE" style="width:90px;"><option value="">Select</option></select></span></td><td><span class="wpcf7-form-control-wrap"><br><select name="SizeE" id="SizeE" style="width:90px;"><option value="">Select</option></select></span></td><td><span class="wpcf7-form-control-wrap"><br><select name="QuantityE" id="QuantityE" style="width:50px;"><option value="1">1</option><option value="2">2</option><option value="3">3</option><option value="4">4</option><option value="5">5</option><option value="6">6</option><option value="7">7</option><option value="8">8</option><option value="9">9</option><option value="10">10</option><option value="11">11</option><option value="12">12</option><option value="13">13</option><option value="14">14</option><option value="15">15</option><option value="16">16</option><option value="17">17</option><option value="18">18</option><option value="19">19</option><option value="20">20</option><option value="21">21</option><option value="22">22</option><option value="23">23</option><option value="24">24</option><option value="25">25</option><option value="26">26</option><option value="27">27</option><option value="28">28</option><option value="29">29</option><option value="30">30</option><option value="31">31</option><option value="32">32</option><option value="33">33</option><option value="34">34</option><option value="35">35</option><option value="36">36</option><option value="37">37</option><option value="38">38</option><option value="39">39</option><option value="40">40</option><option value="41">41</option><option value="42">42</option><option value="43">43</option><option value="44">44</option><option value="45">45</option><option value="46">46</option><option value="47">47</option><option value="48">48</option><option value="49">49</option><option value="50">50</option><option value="51">51</option><option value="52">52</option><option value="53">53</option><option value="54">54</option><option value="55">55</option><option value="56">56</option><option value="57">57</option><option value="58">58</option><option value="59">59</option><option value="60">60</option><option value="61">61</option><option value="62">62</option><option value="63">63</option><option value="64">64</option><option value="65">65</option><option value="66">66</option><option value="67">67</option><option value="68">68</option><option value="69">69</option><option value="70">70</option><option value="71">71</option><option value="72">72</option><option value="73">73</option><option value="74">74</option><option value="75">75</option><option value="76">76</option><option value="77">77</option><option value="78">78</option><option value="79">79</option><option value="80">80</option><option value="81">81</option><option value="82">82</option><option value="83">83</option><option value="84">84</option><option value="85">85</option><option value="86">86</option><option value="87">87</option><option value="88">88</option><option value="89">89</option><option value="90">90</option><option value="91">91</option><option value="92">92</option><option value="93">93</option><option value="94">94</option><option value="95">95</option><option value="96">96</option><option value="97">97</option><option value="98">98</option><option value="99">99</option><option value="100">100</option></select></span></td>
				<td><span class="wpcf7-form-control-wrap">
				<span style="position: relative; bottom: 7px;">
				<div style="width: 100px;overflow: hidden;" id="frontPrintEtext">Not Selected</div>
				<a class="dialog" onclick="SetVar('frontPrintE');" href="#frontPrintE">Select</a>
				<span style="display: none;" id="frontPrintEremove">- <a onclick="remove1('frontPrintE','E');" href="#frontPrintE">Remove</a></span>
				<input type="hidden" name="frontPrintE" id="frontPrintE">
                                    <input value="0" type="hidden" name="frontJumboE" id="frontJumboE">
                                        <input value="0" type="hidden" name="frontUnderbaseE" id="frontUnderbaseE">
                                    
				</span>
				</span></td>
				<td><span class="wpcf7-form-control-wrap">
				<span style="position: relative; bottom: 7px;">
				<div style="width: 100px;overflow: hidden;" id="frontMockupEtext">Not Selected</div>
				<a class="dialog" onclick="SetVar('frontMockupE');" href="#frontMockupE">Select</a>
				<span style="display: none;" id="frontMockupEremove">- <a onclick="remove1('frontMockupE','E');" href="#frontMockupE">Remove</a></span>
				<input type="hidden" name="frontMockupE" id="frontMockupE">
				</span></span></td>
				<td><span class="wpcf7-form-control-wrap" id="BackPrintCellE">
				<span style="position: relative; bottom: 7px;">
				<div style="width: 100px;overflow: hidden;" id="backPrintEtext">Not Selected</div>
				<a class="dialog" onclick="SetVar('backPrintE');" href="#backPrintE">Select</a>
				<span style="display: none;" id="backPrintEremove">- <a onclick="remove1('backPrintE','E');" href="#backPrintE">Remove</a></span>
				<input type="hidden" name="backPrintE" id="backPrintE">
                                <input value="0" type="hidden" name="backJumboE" id="backJumboE">
                                    <input value="0" type="hidden" name="backUnderbaseE" id="backUnderbaseE">
			 	</span></span></td>
				<td><span class="wpcf7-form-control-wrap" id="BackMockupCellE">
				<span style="position: relative; bottom: 7px;">
				<div style="width: 100px;overflow: hidden;" id="backMockupEtext">Not Selected</div>
				<a class="dialog" onclick="SetVar('backMockupE');" href="#backMockupE">Select</a>
				<span style="display: none;" id="backMockupEremove">- <a onclick="remove1('backMockupE','E');" href="#backMockupE">Remove</a></span>
				<input type="hidden" name="backMockupE" id="backMockupE">
                                    
			 </span> </span></td>
				<td><input type="text" style="width:50px;" name="CostE" value="0.00" id="CostE" readonly="">
				<input type="hidden" name="PrintPriceE" value="0" id="PrintPriceE">
                                    
				</td>
				</tr>
				<tr id="holderF" style="display:none;">
				<td><span class="wpcf7-form-control-wrap"><select class="inventory_brand" name="BrandF" id="BrandF" style="width:100px;">
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
<input name="shipping_typeF" id="shipping_typeF" style="display:none;"></span></td><td><span class="wpcf7-form-control-wrap"><input type="hidden" value="1" id="rushedF"><select name="NameF" id="NameF" style="width:100px;"><option value="">Select</option></select></span></td><td><span class="wpcf7-form-control-wrap"><br><select name="ColorF" id="ColorF" style="width:90px;"><option value="">Select</option></select></span></td><td><span class="wpcf7-form-control-wrap"><br><select name="SizeF" id="SizeF" style="width:90px;"><option value="">Select</option></select></span></td><td><span class="wpcf7-form-control-wrap"><br><select name="QuantityF" id="QuantityF" style="width:50px;"><option value="1">1</option><option value="2">2</option><option value="3">3</option><option value="4">4</option><option value="5">5</option><option value="6">6</option><option value="7">7</option><option value="8">8</option><option value="9">9</option><option value="10">10</option><option value="11">11</option><option value="12">12</option><option value="13">13</option><option value="14">14</option><option value="15">15</option><option value="16">16</option><option value="17">17</option><option value="18">18</option><option value="19">19</option><option value="20">20</option><option value="21">21</option><option value="22">22</option><option value="23">23</option><option value="24">24</option><option value="25">25</option><option value="26">26</option><option value="27">27</option><option value="28">28</option><option value="29">29</option><option value="30">30</option><option value="31">31</option><option value="32">32</option><option value="33">33</option><option value="34">34</option><option value="35">35</option><option value="36">36</option><option value="37">37</option><option value="38">38</option><option value="39">39</option><option value="40">40</option><option value="41">41</option><option value="42">42</option><option value="43">43</option><option value="44">44</option><option value="45">45</option><option value="46">46</option><option value="47">47</option><option value="48">48</option><option value="49">49</option><option value="50">50</option><option value="51">51</option><option value="52">52</option><option value="53">53</option><option value="54">54</option><option value="55">55</option><option value="56">56</option><option value="57">57</option><option value="58">58</option><option value="59">59</option><option value="60">60</option><option value="61">61</option><option value="62">62</option><option value="63">63</option><option value="64">64</option><option value="65">65</option><option value="66">66</option><option value="67">67</option><option value="68">68</option><option value="69">69</option><option value="70">70</option><option value="71">71</option><option value="72">72</option><option value="73">73</option><option value="74">74</option><option value="75">75</option><option value="76">76</option><option value="77">77</option><option value="78">78</option><option value="79">79</option><option value="80">80</option><option value="81">81</option><option value="82">82</option><option value="83">83</option><option value="84">84</option><option value="85">85</option><option value="86">86</option><option value="87">87</option><option value="88">88</option><option value="89">89</option><option value="90">90</option><option value="91">91</option><option value="92">92</option><option value="93">93</option><option value="94">94</option><option value="95">95</option><option value="96">96</option><option value="97">97</option><option value="98">98</option><option value="99">99</option><option value="100">100</option></select></span></td>
				<td><span class="wpcf7-form-control-wrap">
				<span style="position: relative; bottom: 7px;">
				<div style="width: 100px;overflow: hidden;" id="frontPrintFtext">Not Selected</div>
				<a class="dialog" onclick="SetVar('frontPrintF');" href="#frontPrintF">Select</a>
				<span style="display: none;" id="frontPrintFremove">- <a onclick="remove1('frontPrintF','F');" href="#frontPrintF">Remove</a></span>
				<input type="hidden" name="frontPrintF" id="frontPrintF">
                                    <input value="0" type="hidden" name="frontJumboF" id="frontJumboF">
                                        <input value="0" type="hidden" name="frontUnderbaseF" id="frontUnderbaseF">
                                    
				</span>
				</span></td>
				<td><span class="wpcf7-form-control-wrap">
				<span style="position: relative; bottom: 7px;">
				<div style="width: 100px;overflow: hidden;" id="frontMockupFtext">Not Selected</div>
				<a class="dialog" onclick="SetVar('frontMockupF');" href="#frontMockupF">Select</a>
				<span style="display: none;" id="frontMockupFremove">- <a onclick="remove1('frontMockupF','F');" href="#frontMockupF">Remove</a></span>
				<input type="hidden" name="frontMockupF" id="frontMockupF">
				</span></span></td>
				<td><span class="wpcf7-form-control-wrap" id="BackPrintCellF">
				<span style="position: relative; bottom: 7px;">
				<div style="width: 100px;overflow: hidden;" id="backPrintFtext">Not Selected</div>
				<a class="dialog" onclick="SetVar('backPrintF');" href="#backPrintF">Select</a>
				<span style="display: none;" id="backPrintFremove">- <a onclick="remove1('backPrintF','F');" href="#backPrintF">Remove</a></span>
				<input type="hidden" name="backPrintF" id="backPrintF">
                                <input value="0" type="hidden" name="backJumboF" id="backJumboF">
                                    <input value="0" type="hidden" name="backUnderbaseF" id="backUnderbaseF">
			 	</span></span></td>
				<td><span class="wpcf7-form-control-wrap" id="BackMockupCellF">
				<span style="position: relative; bottom: 7px;">
				<div style="width: 100px;overflow: hidden;" id="backMockupFtext">Not Selected</div>
				<a class="dialog" onclick="SetVar('backMockupF');" href="#backMockupF">Select</a>
				<span style="display: none;" id="backMockupFremove">- <a onclick="remove1('backMockupF','F');" href="#backMockupF">Remove</a></span>
				<input type="hidden" name="backMockupF" id="backMockupF">
                                    
			 </span> </span></td>
				<td><input type="text" style="width:50px;" name="CostF" value="0.00" id="CostF" readonly="">
				<input type="hidden" name="PrintPriceF" value="0" id="PrintPriceF">
                                    
				</td>
				</tr>
				<tr id="holderG" style="display:none;">
				<td><span class="wpcf7-form-control-wrap"><select class="inventory_brand" name="BrandG" id="BrandG" style="width:100px;">
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
<input name="shipping_typeG" id="shipping_typeG" style="display:none;"></span></td><td><span class="wpcf7-form-control-wrap"><input type="hidden" value="1" id="rushedG"><select name="NameG" id="NameG" style="width:100px;"><option value="">Select</option></select></span></td><td><span class="wpcf7-form-control-wrap"><br><select name="ColorG" id="ColorG" style="width:90px;"><option value="">Select</option></select></span></td><td><span class="wpcf7-form-control-wrap"><br><select name="SizeG" id="SizeG" style="width:90px;"><option value="">Select</option></select></span></td><td><span class="wpcf7-form-control-wrap"><br><select name="QuantityG" id="QuantityG" style="width:50px;"><option value="1">1</option><option value="2">2</option><option value="3">3</option><option value="4">4</option><option value="5">5</option><option value="6">6</option><option value="7">7</option><option value="8">8</option><option value="9">9</option><option value="10">10</option><option value="11">11</option><option value="12">12</option><option value="13">13</option><option value="14">14</option><option value="15">15</option><option value="16">16</option><option value="17">17</option><option value="18">18</option><option value="19">19</option><option value="20">20</option><option value="21">21</option><option value="22">22</option><option value="23">23</option><option value="24">24</option><option value="25">25</option><option value="26">26</option><option value="27">27</option><option value="28">28</option><option value="29">29</option><option value="30">30</option><option value="31">31</option><option value="32">32</option><option value="33">33</option><option value="34">34</option><option value="35">35</option><option value="36">36</option><option value="37">37</option><option value="38">38</option><option value="39">39</option><option value="40">40</option><option value="41">41</option><option value="42">42</option><option value="43">43</option><option value="44">44</option><option value="45">45</option><option value="46">46</option><option value="47">47</option><option value="48">48</option><option value="49">49</option><option value="50">50</option><option value="51">51</option><option value="52">52</option><option value="53">53</option><option value="54">54</option><option value="55">55</option><option value="56">56</option><option value="57">57</option><option value="58">58</option><option value="59">59</option><option value="60">60</option><option value="61">61</option><option value="62">62</option><option value="63">63</option><option value="64">64</option><option value="65">65</option><option value="66">66</option><option value="67">67</option><option value="68">68</option><option value="69">69</option><option value="70">70</option><option value="71">71</option><option value="72">72</option><option value="73">73</option><option value="74">74</option><option value="75">75</option><option value="76">76</option><option value="77">77</option><option value="78">78</option><option value="79">79</option><option value="80">80</option><option value="81">81</option><option value="82">82</option><option value="83">83</option><option value="84">84</option><option value="85">85</option><option value="86">86</option><option value="87">87</option><option value="88">88</option><option value="89">89</option><option value="90">90</option><option value="91">91</option><option value="92">92</option><option value="93">93</option><option value="94">94</option><option value="95">95</option><option value="96">96</option><option value="97">97</option><option value="98">98</option><option value="99">99</option><option value="100">100</option></select></span></td>
				<td><span class="wpcf7-form-control-wrap">
				<span style="position: relative; bottom: 7px;">
				<div style="width: 100px;overflow: hidden;" id="frontPrintGtext">Not Selected</div>
				<a class="dialog" onclick="SetVar('frontPrintG');" href="#frontPrintG">Select</a>
				<span style="display: none;" id="frontPrintGremove">- <a onclick="remove1('frontPrintG','G');" href="#frontPrintG">Remove</a></span>
				<input type="hidden" name="frontPrintG" id="frontPrintG">
                                    <input value="0" type="hidden" name="frontJumboG" id="frontJumboG">
                                        <input value="0" type="hidden" name="frontUnderbaseG" id="frontUnderbaseG">
                                    
				</span>
				</span></td>
				<td><span class="wpcf7-form-control-wrap">
				<span style="position: relative; bottom: 7px;">
				<div style="width: 100px;overflow: hidden;" id="frontMockupGtext">Not Selected</div>
				<a class="dialog" onclick="SetVar('frontMockupG');" href="#frontMockupG">Select</a>
				<span style="display: none;" id="frontMockupGremove">- <a onclick="remove1('frontMockupG','G');" href="#frontMockupG">Remove</a></span>
				<input type="hidden" name="frontMockupG" id="frontMockupG">
				</span></span></td>
				<td><span class="wpcf7-form-control-wrap" id="BackPrintCellG">
				<span style="position: relative; bottom: 7px;">
				<div style="width: 100px;overflow: hidden;" id="backPrintGtext">Not Selected</div>
				<a class="dialog" onclick="SetVar('backPrintG');" href="#backPrintG">Select</a>
				<span style="display: none;" id="backPrintGremove">- <a onclick="remove1('backPrintG','G');" href="#backPrintG">Remove</a></span>
				<input type="hidden" name="backPrintG" id="backPrintG">
                                <input value="0" type="hidden" name="backJumboG" id="backJumboG">
                                    <input value="0" type="hidden" name="backUnderbaseG" id="backUnderbaseG">
			 	</span></span></td>
				<td><span class="wpcf7-form-control-wrap" id="BackMockupCellG">
				<span style="position: relative; bottom: 7px;">
				<div style="width: 100px;overflow: hidden;" id="backMockupGtext">Not Selected</div>
				<a class="dialog" onclick="SetVar('backMockupG');" href="#backMockupG">Select</a>
				<span style="display: none;" id="backMockupGremove">- <a onclick="remove1('backMockupG','G');" href="#backMockupG">Remove</a></span>
				<input type="hidden" name="backMockupG" id="backMockupG">
                                    
			 </span> </span></td>
				<td><input type="text" style="width:50px;" name="CostG" value="0.00" id="CostG" readonly="">
				<input type="hidden" name="PrintPriceG" value="0" id="PrintPriceG">
                                    
				</td>
				</tr>
				<tr id="holderH" style="display:none;">
				<td><span class="wpcf7-form-control-wrap"><select class="inventory_brand" name="BrandH" id="BrandH" style="width:100px;">
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
<input name="shipping_typeH" id="shipping_typeH" style="display:none;"></span></td><td><span class="wpcf7-form-control-wrap"><input type="hidden" value="1" id="rushedH"><select name="NameH" id="NameH" style="width:100px;"><option value="">Select</option></select></span></td><td><span class="wpcf7-form-control-wrap"><br><select name="ColorH" id="ColorH" style="width:90px;"><option value="">Select</option></select></span></td><td><span class="wpcf7-form-control-wrap"><br><select name="SizeH" id="SizeH" style="width:90px;"><option value="">Select</option></select></span></td><td><span class="wpcf7-form-control-wrap"><br><select name="QuantityH" id="QuantityH" style="width:50px;"><option value="1">1</option><option value="2">2</option><option value="3">3</option><option value="4">4</option><option value="5">5</option><option value="6">6</option><option value="7">7</option><option value="8">8</option><option value="9">9</option><option value="10">10</option><option value="11">11</option><option value="12">12</option><option value="13">13</option><option value="14">14</option><option value="15">15</option><option value="16">16</option><option value="17">17</option><option value="18">18</option><option value="19">19</option><option value="20">20</option><option value="21">21</option><option value="22">22</option><option value="23">23</option><option value="24">24</option><option value="25">25</option><option value="26">26</option><option value="27">27</option><option value="28">28</option><option value="29">29</option><option value="30">30</option><option value="31">31</option><option value="32">32</option><option value="33">33</option><option value="34">34</option><option value="35">35</option><option value="36">36</option><option value="37">37</option><option value="38">38</option><option value="39">39</option><option value="40">40</option><option value="41">41</option><option value="42">42</option><option value="43">43</option><option value="44">44</option><option value="45">45</option><option value="46">46</option><option value="47">47</option><option value="48">48</option><option value="49">49</option><option value="50">50</option><option value="51">51</option><option value="52">52</option><option value="53">53</option><option value="54">54</option><option value="55">55</option><option value="56">56</option><option value="57">57</option><option value="58">58</option><option value="59">59</option><option value="60">60</option><option value="61">61</option><option value="62">62</option><option value="63">63</option><option value="64">64</option><option value="65">65</option><option value="66">66</option><option value="67">67</option><option value="68">68</option><option value="69">69</option><option value="70">70</option><option value="71">71</option><option value="72">72</option><option value="73">73</option><option value="74">74</option><option value="75">75</option><option value="76">76</option><option value="77">77</option><option value="78">78</option><option value="79">79</option><option value="80">80</option><option value="81">81</option><option value="82">82</option><option value="83">83</option><option value="84">84</option><option value="85">85</option><option value="86">86</option><option value="87">87</option><option value="88">88</option><option value="89">89</option><option value="90">90</option><option value="91">91</option><option value="92">92</option><option value="93">93</option><option value="94">94</option><option value="95">95</option><option value="96">96</option><option value="97">97</option><option value="98">98</option><option value="99">99</option><option value="100">100</option></select></span></td>
				<td><span class="wpcf7-form-control-wrap">
				<span style="position: relative; bottom: 7px;">
				<div style="width: 100px;overflow: hidden;" id="frontPrintHtext">Not Selected</div>
				<a class="dialog" onclick="SetVar('frontPrintH');" href="#frontPrintH">Select</a>
				<span style="display: none;" id="frontPrintHremove">- <a onclick="remove1('frontPrintH','H');" href="#frontPrintH">Remove</a></span>
				<input type="hidden" name="frontPrintH" id="frontPrintH">
                                    <input value="0" type="hidden" name="frontJumboH" id="frontJumboH">
                                        <input value="0" type="hidden" name="frontUnderbaseH" id="frontUnderbaseH">
                                    
				</span>
				</span></td>
				<td><span class="wpcf7-form-control-wrap">
				<span style="position: relative; bottom: 7px;">
				<div style="width: 100px;overflow: hidden;" id="frontMockupHtext">Not Selected</div>
				<a class="dialog" onclick="SetVar('frontMockupH');" href="#frontMockupH">Select</a>
				<span style="display: none;" id="frontMockupHremove">- <a onclick="remove1('frontMockupH','H');" href="#frontMockupH">Remove</a></span>
				<input type="hidden" name="frontMockupH" id="frontMockupH">
				</span></span></td>
				<td><span class="wpcf7-form-control-wrap" id="BackPrintCellH">
				<span style="position: relative; bottom: 7px;">
				<div style="width: 100px;overflow: hidden;" id="backPrintHtext">Not Selected</div>
				<a class="dialog" onclick="SetVar('backPrintH');" href="#backPrintH">Select</a>
				<span style="display: none;" id="backPrintHremove">- <a onclick="remove1('backPrintH','H');" href="#backPrintH">Remove</a></span>
				<input type="hidden" name="backPrintH" id="backPrintH">
                                <input value="0" type="hidden" name="backJumboH" id="backJumboH">
                                    <input value="0" type="hidden" name="backUnderbaseH" id="backUnderbaseH">
			 	</span></span></td>
				<td><span class="wpcf7-form-control-wrap" id="BackMockupCellH">
				<span style="position: relative; bottom: 7px;">
				<div style="width: 100px;overflow: hidden;" id="backMockupHtext">Not Selected</div>
				<a class="dialog" onclick="SetVar('backMockupH');" href="#backMockupH">Select</a>
				<span style="display: none;" id="backMockupHremove">- <a onclick="remove1('backMockupH','H');" href="#backMockupH">Remove</a></span>
				<input type="hidden" name="backMockupH" id="backMockupH">
                                    
			 </span> </span></td>
				<td><input type="text" style="width:50px;" name="CostH" value="0.00" id="CostH" readonly="">
				<input type="hidden" name="PrintPriceH" value="0" id="PrintPriceH">
                                    
				</td>
				</tr>
				<tr id="holderI" style="display:none;">
				<td><span class="wpcf7-form-control-wrap"><select class="inventory_brand" name="BrandI" id="BrandI" style="width:100px;">
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
<input name="shipping_typeI" id="shipping_typeI" style="display:none;"></span></td><td><span class="wpcf7-form-control-wrap"><input type="hidden" value="1" id="rushedI"><select name="NameI" id="NameI" style="width:100px;"><option value="">Select</option></select></span></td><td><span class="wpcf7-form-control-wrap"><br><select name="ColorI" id="ColorI" style="width:90px;"><option value="">Select</option></select></span></td><td><span class="wpcf7-form-control-wrap"><br><select name="SizeI" id="SizeI" style="width:90px;"><option value="">Select</option></select></span></td><td><span class="wpcf7-form-control-wrap"><br><select name="QuantityI" id="QuantityI" style="width:50px;"><option value="1">1</option><option value="2">2</option><option value="3">3</option><option value="4">4</option><option value="5">5</option><option value="6">6</option><option value="7">7</option><option value="8">8</option><option value="9">9</option><option value="10">10</option><option value="11">11</option><option value="12">12</option><option value="13">13</option><option value="14">14</option><option value="15">15</option><option value="16">16</option><option value="17">17</option><option value="18">18</option><option value="19">19</option><option value="20">20</option><option value="21">21</option><option value="22">22</option><option value="23">23</option><option value="24">24</option><option value="25">25</option><option value="26">26</option><option value="27">27</option><option value="28">28</option><option value="29">29</option><option value="30">30</option><option value="31">31</option><option value="32">32</option><option value="33">33</option><option value="34">34</option><option value="35">35</option><option value="36">36</option><option value="37">37</option><option value="38">38</option><option value="39">39</option><option value="40">40</option><option value="41">41</option><option value="42">42</option><option value="43">43</option><option value="44">44</option><option value="45">45</option><option value="46">46</option><option value="47">47</option><option value="48">48</option><option value="49">49</option><option value="50">50</option><option value="51">51</option><option value="52">52</option><option value="53">53</option><option value="54">54</option><option value="55">55</option><option value="56">56</option><option value="57">57</option><option value="58">58</option><option value="59">59</option><option value="60">60</option><option value="61">61</option><option value="62">62</option><option value="63">63</option><option value="64">64</option><option value="65">65</option><option value="66">66</option><option value="67">67</option><option value="68">68</option><option value="69">69</option><option value="70">70</option><option value="71">71</option><option value="72">72</option><option value="73">73</option><option value="74">74</option><option value="75">75</option><option value="76">76</option><option value="77">77</option><option value="78">78</option><option value="79">79</option><option value="80">80</option><option value="81">81</option><option value="82">82</option><option value="83">83</option><option value="84">84</option><option value="85">85</option><option value="86">86</option><option value="87">87</option><option value="88">88</option><option value="89">89</option><option value="90">90</option><option value="91">91</option><option value="92">92</option><option value="93">93</option><option value="94">94</option><option value="95">95</option><option value="96">96</option><option value="97">97</option><option value="98">98</option><option value="99">99</option><option value="100">100</option></select></span></td>
				<td><span class="wpcf7-form-control-wrap">
				<span style="position: relative; bottom: 7px;">
				<div style="width: 100px;overflow: hidden;" id="frontPrintItext">Not Selected</div>
				<a class="dialog" onclick="SetVar('frontPrintI');" href="#frontPrintI">Select</a>
				<span style="display: none;" id="frontPrintIremove">- <a onclick="remove1('frontPrintI','I');" href="#frontPrintI">Remove</a></span>
				<input type="hidden" name="frontPrintI" id="frontPrintI">
                                    <input value="0" type="hidden" name="frontJumboI" id="frontJumboI">
                                        <input value="0" type="hidden" name="frontUnderbaseI" id="frontUnderbaseI">
                                    
				</span>
				</span></td>
				<td><span class="wpcf7-form-control-wrap">
				<span style="position: relative; bottom: 7px;">
				<div style="width: 100px;overflow: hidden;" id="frontMockupItext">Not Selected</div>
				<a class="dialog" onclick="SetVar('frontMockupI');" href="#frontMockupI">Select</a>
				<span style="display: none;" id="frontMockupIremove">- <a onclick="remove1('frontMockupI','I');" href="#frontMockupI">Remove</a></span>
				<input type="hidden" name="frontMockupI" id="frontMockupI">
				</span></span></td>
				<td><span class="wpcf7-form-control-wrap" id="BackPrintCellI">
				<span style="position: relative; bottom: 7px;">
				<div style="width: 100px;overflow: hidden;" id="backPrintItext">Not Selected</div>
				<a class="dialog" onclick="SetVar('backPrintI');" href="#backPrintI">Select</a>
				<span style="display: none;" id="backPrintIremove">- <a onclick="remove1('backPrintI','I');" href="#backPrintI">Remove</a></span>
				<input type="hidden" name="backPrintI" id="backPrintI">
                                <input value="0" type="hidden" name="backJumboI" id="backJumboI">
                                    <input value="0" type="hidden" name="backUnderbaseI" id="backUnderbaseI">
			 	</span></span></td>
				<td><span class="wpcf7-form-control-wrap" id="BackMockupCellI">
				<span style="position: relative; bottom: 7px;">
				<div style="width: 100px;overflow: hidden;" id="backMockupItext">Not Selected</div>
				<a class="dialog" onclick="SetVar('backMockupI');" href="#backMockupI">Select</a>
				<span style="display: none;" id="backMockupIremove">- <a onclick="remove1('backMockupI','I');" href="#backMockupI">Remove</a></span>
				<input type="hidden" name="backMockupI" id="backMockupI">
                                    
			 </span> </span></td>
				<td><input type="text" style="width:50px;" name="CostI" value="0.00" id="CostI" readonly="">
				<input type="hidden" name="PrintPriceI" value="0" id="PrintPriceI">
                                    
				</td>
				</tr>
				<tr id="holderJ" style="display:none;">
				<td><span class="wpcf7-form-control-wrap"><select class="inventory_brand" name="BrandJ" id="BrandJ" style="width:100px;">
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
<input name="shipping_typeJ" id="shipping_typeJ" style="display:none;"></span></td><td><span class="wpcf7-form-control-wrap"><input type="hidden" value="1" id="rushedJ"><select name="NameJ" id="NameJ" style="width:100px;"><option value="">Select</option></select></span></td><td><span class="wpcf7-form-control-wrap"><br><select name="ColorJ" id="ColorJ" style="width:90px;"><option value="">Select</option></select></span></td><td><span class="wpcf7-form-control-wrap"><br><select name="SizeJ" id="SizeJ" style="width:90px;"><option value="">Select</option></select></span></td><td><span class="wpcf7-form-control-wrap"><br><select name="QuantityJ" id="QuantityJ" style="width:50px;"><option value="1">1</option><option value="2">2</option><option value="3">3</option><option value="4">4</option><option value="5">5</option><option value="6">6</option><option value="7">7</option><option value="8">8</option><option value="9">9</option><option value="10">10</option><option value="11">11</option><option value="12">12</option><option value="13">13</option><option value="14">14</option><option value="15">15</option><option value="16">16</option><option value="17">17</option><option value="18">18</option><option value="19">19</option><option value="20">20</option><option value="21">21</option><option value="22">22</option><option value="23">23</option><option value="24">24</option><option value="25">25</option><option value="26">26</option><option value="27">27</option><option value="28">28</option><option value="29">29</option><option value="30">30</option><option value="31">31</option><option value="32">32</option><option value="33">33</option><option value="34">34</option><option value="35">35</option><option value="36">36</option><option value="37">37</option><option value="38">38</option><option value="39">39</option><option value="40">40</option><option value="41">41</option><option value="42">42</option><option value="43">43</option><option value="44">44</option><option value="45">45</option><option value="46">46</option><option value="47">47</option><option value="48">48</option><option value="49">49</option><option value="50">50</option><option value="51">51</option><option value="52">52</option><option value="53">53</option><option value="54">54</option><option value="55">55</option><option value="56">56</option><option value="57">57</option><option value="58">58</option><option value="59">59</option><option value="60">60</option><option value="61">61</option><option value="62">62</option><option value="63">63</option><option value="64">64</option><option value="65">65</option><option value="66">66</option><option value="67">67</option><option value="68">68</option><option value="69">69</option><option value="70">70</option><option value="71">71</option><option value="72">72</option><option value="73">73</option><option value="74">74</option><option value="75">75</option><option value="76">76</option><option value="77">77</option><option value="78">78</option><option value="79">79</option><option value="80">80</option><option value="81">81</option><option value="82">82</option><option value="83">83</option><option value="84">84</option><option value="85">85</option><option value="86">86</option><option value="87">87</option><option value="88">88</option><option value="89">89</option><option value="90">90</option><option value="91">91</option><option value="92">92</option><option value="93">93</option><option value="94">94</option><option value="95">95</option><option value="96">96</option><option value="97">97</option><option value="98">98</option><option value="99">99</option><option value="100">100</option></select></span></td>
				<td><span class="wpcf7-form-control-wrap">
				<span style="position: relative; bottom: 7px;">
				<div style="width: 100px;overflow: hidden;" id="frontPrintJtext">Not Selected</div>
				<a class="dialog" onclick="SetVar('frontPrintJ');" href="#frontPrintJ">Select</a>
				<span style="display: none;" id="frontPrintJremove">- <a onclick="remove1('frontPrintJ','J');" href="#frontPrintJ">Remove</a></span>
				<input type="hidden" name="frontPrintJ" id="frontPrintJ">
                                    <input value="0" type="hidden" name="frontJumboJ" id="frontJumboJ">
                                        <input value="0" type="hidden" name="frontUnderbaseJ" id="frontUnderbaseJ">
                                    
				</span>
				</span></td>
				<td><span class="wpcf7-form-control-wrap">
				<span style="position: relative; bottom: 7px;">
				<div style="width: 100px;overflow: hidden;" id="frontMockupJtext">Not Selected</div>
				<a class="dialog" onclick="SetVar('frontMockupJ');" href="#frontMockupJ">Select</a>
				<span style="display: none;" id="frontMockupJremove">- <a onclick="remove1('frontMockupJ','J');" href="#frontMockupJ">Remove</a></span>
				<input type="hidden" name="frontMockupJ" id="frontMockupJ">
				</span></span></td>
				<td><span class="wpcf7-form-control-wrap" id="BackPrintCellJ">
				<span style="position: relative; bottom: 7px;">
				<div style="width: 100px;overflow: hidden;" id="backPrintJtext">Not Selected</div>
				<a class="dialog" onclick="SetVar('backPrintJ');" href="#backPrintJ">Select</a>
				<span style="display: none;" id="backPrintJremove">- <a onclick="remove1('backPrintJ','J');" href="#backPrintJ">Remove</a></span>
				<input type="hidden" name="backPrintJ" id="backPrintJ">
                                <input value="0" type="hidden" name="backJumboJ" id="backJumboJ">
                                    <input value="0" type="hidden" name="backUnderbaseJ" id="backUnderbaseJ">
			 	</span></span></td>
				<td><span class="wpcf7-form-control-wrap" id="BackMockupCellJ">
				<span style="position: relative; bottom: 7px;">
				<div style="width: 100px;overflow: hidden;" id="backMockupJtext">Not Selected</div>
				<a class="dialog" onclick="SetVar('backMockupJ');" href="#backMockupJ">Select</a>
				<span style="display: none;" id="backMockupJremove">- <a onclick="remove1('backMockupJ','J');" href="#backMockupJ">Remove</a></span>
				<input type="hidden" name="backMockupJ" id="backMockupJ">
                                    
			 </span> </span></td>
				<td><input type="text" style="width:50px;" name="CostJ" value="0.00" id="CostJ" readonly="">
				<input type="hidden" name="PrintPriceJ" value="0" id="PrintPriceJ">
                                    
				</td>
				</tr>
				<tr id="holderK" style="display:none;">
				<td><span class="wpcf7-form-control-wrap"><select class="inventory_brand" name="BrandK" id="BrandK" style="width:100px;">
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
<input name="shipping_typeK" id="shipping_typeK" style="display:none;"></span></td><td><span class="wpcf7-form-control-wrap"><input type="hidden" value="1" id="rushedK"><select name="NameK" id="NameK" style="width:100px;"><option value="">Select</option></select></span></td><td><span class="wpcf7-form-control-wrap"><br><select name="ColorK" id="ColorK" style="width:90px;"><option value="">Select</option></select></span></td><td><span class="wpcf7-form-control-wrap"><br><select name="SizeK" id="SizeK" style="width:90px;"><option value="">Select</option></select></span></td><td><span class="wpcf7-form-control-wrap"><br><select name="QuantityK" id="QuantityK" style="width:50px;"><option value="1">1</option><option value="2">2</option><option value="3">3</option><option value="4">4</option><option value="5">5</option><option value="6">6</option><option value="7">7</option><option value="8">8</option><option value="9">9</option><option value="10">10</option><option value="11">11</option><option value="12">12</option><option value="13">13</option><option value="14">14</option><option value="15">15</option><option value="16">16</option><option value="17">17</option><option value="18">18</option><option value="19">19</option><option value="20">20</option><option value="21">21</option><option value="22">22</option><option value="23">23</option><option value="24">24</option><option value="25">25</option><option value="26">26</option><option value="27">27</option><option value="28">28</option><option value="29">29</option><option value="30">30</option><option value="31">31</option><option value="32">32</option><option value="33">33</option><option value="34">34</option><option value="35">35</option><option value="36">36</option><option value="37">37</option><option value="38">38</option><option value="39">39</option><option value="40">40</option><option value="41">41</option><option value="42">42</option><option value="43">43</option><option value="44">44</option><option value="45">45</option><option value="46">46</option><option value="47">47</option><option value="48">48</option><option value="49">49</option><option value="50">50</option><option value="51">51</option><option value="52">52</option><option value="53">53</option><option value="54">54</option><option value="55">55</option><option value="56">56</option><option value="57">57</option><option value="58">58</option><option value="59">59</option><option value="60">60</option><option value="61">61</option><option value="62">62</option><option value="63">63</option><option value="64">64</option><option value="65">65</option><option value="66">66</option><option value="67">67</option><option value="68">68</option><option value="69">69</option><option value="70">70</option><option value="71">71</option><option value="72">72</option><option value="73">73</option><option value="74">74</option><option value="75">75</option><option value="76">76</option><option value="77">77</option><option value="78">78</option><option value="79">79</option><option value="80">80</option><option value="81">81</option><option value="82">82</option><option value="83">83</option><option value="84">84</option><option value="85">85</option><option value="86">86</option><option value="87">87</option><option value="88">88</option><option value="89">89</option><option value="90">90</option><option value="91">91</option><option value="92">92</option><option value="93">93</option><option value="94">94</option><option value="95">95</option><option value="96">96</option><option value="97">97</option><option value="98">98</option><option value="99">99</option><option value="100">100</option></select></span></td>
				<td><span class="wpcf7-form-control-wrap">
				<span style="position: relative; bottom: 7px;">
				<div style="width: 100px;overflow: hidden;" id="frontPrintKtext">Not Selected</div>
				<a class="dialog" onclick="SetVar('frontPrintK');" href="#frontPrintK">Select</a>
				<span style="display: none;" id="frontPrintKremove">- <a onclick="remove1('frontPrintK','K');" href="#frontPrintK">Remove</a></span>
				<input type="hidden" name="frontPrintK" id="frontPrintK">
                                    <input value="0" type="hidden" name="frontJumboK" id="frontJumboK">
                                        <input value="0" type="hidden" name="frontUnderbaseK" id="frontUnderbaseK">
                                    
				</span>
				</span></td>
				<td><span class="wpcf7-form-control-wrap">
				<span style="position: relative; bottom: 7px;">
				<div style="width: 100px;overflow: hidden;" id="frontMockupKtext">Not Selected</div>
				<a class="dialog" onclick="SetVar('frontMockupK');" href="#frontMockupK">Select</a>
				<span style="display: none;" id="frontMockupKremove">- <a onclick="remove1('frontMockupK','K');" href="#frontMockupK">Remove</a></span>
				<input type="hidden" name="frontMockupK" id="frontMockupK">
				</span></span></td>
				<td><span class="wpcf7-form-control-wrap" id="BackPrintCellK">
				<span style="position: relative; bottom: 7px;">
				<div style="width: 100px;overflow: hidden;" id="backPrintKtext">Not Selected</div>
				<a class="dialog" onclick="SetVar('backPrintK');" href="#backPrintK">Select</a>
				<span style="display: none;" id="backPrintKremove">- <a onclick="remove1('backPrintK','K');" href="#backPrintK">Remove</a></span>
				<input type="hidden" name="backPrintK" id="backPrintK">
                                <input value="0" type="hidden" name="backJumboK" id="backJumboK">
                                    <input value="0" type="hidden" name="backUnderbaseK" id="backUnderbaseK">
			 	</span></span></td>
				<td><span class="wpcf7-form-control-wrap" id="BackMockupCellK">
				<span style="position: relative; bottom: 7px;">
				<div style="width: 100px;overflow: hidden;" id="backMockupKtext">Not Selected</div>
				<a class="dialog" onclick="SetVar('backMockupK');" href="#backMockupK">Select</a>
				<span style="display: none;" id="backMockupKremove">- <a onclick="remove1('backMockupK','K');" href="#backMockupK">Remove</a></span>
				<input type="hidden" name="backMockupK" id="backMockupK">
                                    
			 </span> </span></td>
				<td><input type="text" style="width:50px;" name="CostK" value="0.00" id="CostK" readonly="">
				<input type="hidden" name="PrintPriceK" value="0" id="PrintPriceK">
                                    
				</td>
				</tr>
				<tr id="holderL" style="display:none;">
				<td><span class="wpcf7-form-control-wrap"><select class="inventory_brand" name="BrandL" id="BrandL" style="width:100px;">
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
<input name="shipping_typeL" id="shipping_typeL" style="display:none;"></span></td><td><span class="wpcf7-form-control-wrap"><input type="hidden" value="1" id="rushedL"><select name="NameL" id="NameL" style="width:100px;"><option value="">Select</option></select></span></td><td><span class="wpcf7-form-control-wrap"><br><select name="ColorL" id="ColorL" style="width:90px;"><option value="">Select</option></select></span></td><td><span class="wpcf7-form-control-wrap"><br><select name="SizeL" id="SizeL" style="width:90px;"><option value="">Select</option></select></span></td><td><span class="wpcf7-form-control-wrap"><br><select name="QuantityL" id="QuantityL" style="width:50px;"><option value="1">1</option><option value="2">2</option><option value="3">3</option><option value="4">4</option><option value="5">5</option><option value="6">6</option><option value="7">7</option><option value="8">8</option><option value="9">9</option><option value="10">10</option><option value="11">11</option><option value="12">12</option><option value="13">13</option><option value="14">14</option><option value="15">15</option><option value="16">16</option><option value="17">17</option><option value="18">18</option><option value="19">19</option><option value="20">20</option><option value="21">21</option><option value="22">22</option><option value="23">23</option><option value="24">24</option><option value="25">25</option><option value="26">26</option><option value="27">27</option><option value="28">28</option><option value="29">29</option><option value="30">30</option><option value="31">31</option><option value="32">32</option><option value="33">33</option><option value="34">34</option><option value="35">35</option><option value="36">36</option><option value="37">37</option><option value="38">38</option><option value="39">39</option><option value="40">40</option><option value="41">41</option><option value="42">42</option><option value="43">43</option><option value="44">44</option><option value="45">45</option><option value="46">46</option><option value="47">47</option><option value="48">48</option><option value="49">49</option><option value="50">50</option><option value="51">51</option><option value="52">52</option><option value="53">53</option><option value="54">54</option><option value="55">55</option><option value="56">56</option><option value="57">57</option><option value="58">58</option><option value="59">59</option><option value="60">60</option><option value="61">61</option><option value="62">62</option><option value="63">63</option><option value="64">64</option><option value="65">65</option><option value="66">66</option><option value="67">67</option><option value="68">68</option><option value="69">69</option><option value="70">70</option><option value="71">71</option><option value="72">72</option><option value="73">73</option><option value="74">74</option><option value="75">75</option><option value="76">76</option><option value="77">77</option><option value="78">78</option><option value="79">79</option><option value="80">80</option><option value="81">81</option><option value="82">82</option><option value="83">83</option><option value="84">84</option><option value="85">85</option><option value="86">86</option><option value="87">87</option><option value="88">88</option><option value="89">89</option><option value="90">90</option><option value="91">91</option><option value="92">92</option><option value="93">93</option><option value="94">94</option><option value="95">95</option><option value="96">96</option><option value="97">97</option><option value="98">98</option><option value="99">99</option><option value="100">100</option></select></span></td>
				<td><span class="wpcf7-form-control-wrap">
				<span style="position: relative; bottom: 7px;">
				<div style="width: 100px;overflow: hidden;" id="frontPrintLtext">Not Selected</div>
				<a class="dialog" onclick="SetVar('frontPrintL');" href="#frontPrintL">Select</a>
				<span style="display: none;" id="frontPrintLremove">- <a onclick="remove1('frontPrintL','L');" href="#frontPrintL">Remove</a></span>
				<input type="hidden" name="frontPrintL" id="frontPrintL">
                                    <input value="0" type="hidden" name="frontJumboL" id="frontJumboL">
                                        <input value="0" type="hidden" name="frontUnderbaseL" id="frontUnderbaseL">
                                    
				</span>
				</span></td>
				<td><span class="wpcf7-form-control-wrap">
				<span style="position: relative; bottom: 7px;">
				<div style="width: 100px;overflow: hidden;" id="frontMockupLtext">Not Selected</div>
				<a class="dialog" onclick="SetVar('frontMockupL');" href="#frontMockupL">Select</a>
				<span style="display: none;" id="frontMockupLremove">- <a onclick="remove1('frontMockupL','L');" href="#frontMockupL">Remove</a></span>
				<input type="hidden" name="frontMockupL" id="frontMockupL">
				</span></span></td>
				<td><span class="wpcf7-form-control-wrap" id="BackPrintCellL">
				<span style="position: relative; bottom: 7px;">
				<div style="width: 100px;overflow: hidden;" id="backPrintLtext">Not Selected</div>
				<a class="dialog" onclick="SetVar('backPrintL');" href="#backPrintL">Select</a>
				<span style="display: none;" id="backPrintLremove">- <a onclick="remove1('backPrintL','L');" href="#backPrintL">Remove</a></span>
				<input type="hidden" name="backPrintL" id="backPrintL">
                                <input value="0" type="hidden" name="backJumboL" id="backJumboL">
                                    <input value="0" type="hidden" name="backUnderbaseL" id="backUnderbaseL">
			 	</span></span></td>
				<td><span class="wpcf7-form-control-wrap" id="BackMockupCellL">
				<span style="position: relative; bottom: 7px;">
				<div style="width: 100px;overflow: hidden;" id="backMockupLtext">Not Selected</div>
				<a class="dialog" onclick="SetVar('backMockupL');" href="#backMockupL">Select</a>
				<span style="display: none;" id="backMockupLremove">- <a onclick="remove1('backMockupL','L');" href="#backMockupL">Remove</a></span>
				<input type="hidden" name="backMockupL" id="backMockupL">
                                    
			 </span> </span></td>
				<td><input type="text" style="width:50px;" name="CostL" value="0.00" id="CostL" readonly="">
				<input type="hidden" name="PrintPriceL" value="0" id="PrintPriceL">
                                    
				</td>
				</tr>
				<tr id="holderM" style="display:none;">
				<td><span class="wpcf7-form-control-wrap"><select class="inventory_brand" name="BrandM" id="BrandM" style="width:100px;">
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
<input name="shipping_typeM" id="shipping_typeM" style="display:none;"></span></td><td><span class="wpcf7-form-control-wrap"><input type="hidden" value="1" id="rushedM"><select name="NameM" id="NameM" style="width:100px;"><option value="">Select</option></select></span></td><td><span class="wpcf7-form-control-wrap"><br><select name="ColorM" id="ColorM" style="width:90px;"><option value="">Select</option></select></span></td><td><span class="wpcf7-form-control-wrap"><br><select name="SizeM" id="SizeM" style="width:90px;"><option value="">Select</option></select></span></td><td><span class="wpcf7-form-control-wrap"><br><select name="QuantityM" id="QuantityM" style="width:50px;"><option value="1">1</option><option value="2">2</option><option value="3">3</option><option value="4">4</option><option value="5">5</option><option value="6">6</option><option value="7">7</option><option value="8">8</option><option value="9">9</option><option value="10">10</option><option value="11">11</option><option value="12">12</option><option value="13">13</option><option value="14">14</option><option value="15">15</option><option value="16">16</option><option value="17">17</option><option value="18">18</option><option value="19">19</option><option value="20">20</option><option value="21">21</option><option value="22">22</option><option value="23">23</option><option value="24">24</option><option value="25">25</option><option value="26">26</option><option value="27">27</option><option value="28">28</option><option value="29">29</option><option value="30">30</option><option value="31">31</option><option value="32">32</option><option value="33">33</option><option value="34">34</option><option value="35">35</option><option value="36">36</option><option value="37">37</option><option value="38">38</option><option value="39">39</option><option value="40">40</option><option value="41">41</option><option value="42">42</option><option value="43">43</option><option value="44">44</option><option value="45">45</option><option value="46">46</option><option value="47">47</option><option value="48">48</option><option value="49">49</option><option value="50">50</option><option value="51">51</option><option value="52">52</option><option value="53">53</option><option value="54">54</option><option value="55">55</option><option value="56">56</option><option value="57">57</option><option value="58">58</option><option value="59">59</option><option value="60">60</option><option value="61">61</option><option value="62">62</option><option value="63">63</option><option value="64">64</option><option value="65">65</option><option value="66">66</option><option value="67">67</option><option value="68">68</option><option value="69">69</option><option value="70">70</option><option value="71">71</option><option value="72">72</option><option value="73">73</option><option value="74">74</option><option value="75">75</option><option value="76">76</option><option value="77">77</option><option value="78">78</option><option value="79">79</option><option value="80">80</option><option value="81">81</option><option value="82">82</option><option value="83">83</option><option value="84">84</option><option value="85">85</option><option value="86">86</option><option value="87">87</option><option value="88">88</option><option value="89">89</option><option value="90">90</option><option value="91">91</option><option value="92">92</option><option value="93">93</option><option value="94">94</option><option value="95">95</option><option value="96">96</option><option value="97">97</option><option value="98">98</option><option value="99">99</option><option value="100">100</option></select></span></td>
				<td><span class="wpcf7-form-control-wrap">
				<span style="position: relative; bottom: 7px;">
				<div style="width: 100px;overflow: hidden;" id="frontPrintMtext">Not Selected</div>
				<a class="dialog" onclick="SetVar('frontPrintM');" href="#frontPrintM">Select</a>
				<span style="display: none;" id="frontPrintMremove">- <a onclick="remove1('frontPrintM','M');" href="#frontPrintM">Remove</a></span>
				<input type="hidden" name="frontPrintM" id="frontPrintM">
                                    <input value="0" type="hidden" name="frontJumboM" id="frontJumboM">
                                        <input value="0" type="hidden" name="frontUnderbaseM" id="frontUnderbaseM">
                                    
				</span>
				</span></td>
				<td><span class="wpcf7-form-control-wrap">
				<span style="position: relative; bottom: 7px;">
				<div style="width: 100px;overflow: hidden;" id="frontMockupMtext">Not Selected</div>
				<a class="dialog" onclick="SetVar('frontMockupM');" href="#frontMockupM">Select</a>
				<span style="display: none;" id="frontMockupMremove">- <a onclick="remove1('frontMockupM','M');" href="#frontMockupM">Remove</a></span>
				<input type="hidden" name="frontMockupM" id="frontMockupM">
				</span></span></td>
				<td><span class="wpcf7-form-control-wrap" id="BackPrintCellM">
				<span style="position: relative; bottom: 7px;">
				<div style="width: 100px;overflow: hidden;" id="backPrintMtext">Not Selected</div>
				<a class="dialog" onclick="SetVar('backPrintM');" href="#backPrintM">Select</a>
				<span style="display: none;" id="backPrintMremove">- <a onclick="remove1('backPrintM','M');" href="#backPrintM">Remove</a></span>
				<input type="hidden" name="backPrintM" id="backPrintM">
                                <input value="0" type="hidden" name="backJumboM" id="backJumboM">
                                    <input value="0" type="hidden" name="backUnderbaseM" id="backUnderbaseM">
			 	</span></span></td>
				<td><span class="wpcf7-form-control-wrap" id="BackMockupCellM">
				<span style="position: relative; bottom: 7px;">
				<div style="width: 100px;overflow: hidden;" id="backMockupMtext">Not Selected</div>
				<a class="dialog" onclick="SetVar('backMockupM');" href="#backMockupM">Select</a>
				<span style="display: none;" id="backMockupMremove">- <a onclick="remove1('backMockupM','M');" href="#backMockupM">Remove</a></span>
				<input type="hidden" name="backMockupM" id="backMockupM">
                                    
			 </span> </span></td>
				<td><input type="text" style="width:50px;" name="CostM" value="0.00" id="CostM" readonly="">
				<input type="hidden" name="PrintPriceM" value="0" id="PrintPriceM">
                                    
				</td>
				</tr>
				<tr id="holderN" style="display:none;">
				<td><span class="wpcf7-form-control-wrap"><select class="inventory_brand" name="BrandN" id="BrandN" style="width:100px;">
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
<input name="shipping_typeN" id="shipping_typeN" style="display:none;"></span></td><td><span class="wpcf7-form-control-wrap"><input type="hidden" value="1" id="rushedN"><select name="NameN" id="NameN" style="width:100px;"><option value="">Select</option></select></span></td><td><span class="wpcf7-form-control-wrap"><br><select name="ColorN" id="ColorN" style="width:90px;"><option value="">Select</option></select></span></td><td><span class="wpcf7-form-control-wrap"><br><select name="SizeN" id="SizeN" style="width:90px;"><option value="">Select</option></select></span></td><td><span class="wpcf7-form-control-wrap"><br><select name="QuantityN" id="QuantityN" style="width:50px;"><option value="1">1</option><option value="2">2</option><option value="3">3</option><option value="4">4</option><option value="5">5</option><option value="6">6</option><option value="7">7</option><option value="8">8</option><option value="9">9</option><option value="10">10</option><option value="11">11</option><option value="12">12</option><option value="13">13</option><option value="14">14</option><option value="15">15</option><option value="16">16</option><option value="17">17</option><option value="18">18</option><option value="19">19</option><option value="20">20</option><option value="21">21</option><option value="22">22</option><option value="23">23</option><option value="24">24</option><option value="25">25</option><option value="26">26</option><option value="27">27</option><option value="28">28</option><option value="29">29</option><option value="30">30</option><option value="31">31</option><option value="32">32</option><option value="33">33</option><option value="34">34</option><option value="35">35</option><option value="36">36</option><option value="37">37</option><option value="38">38</option><option value="39">39</option><option value="40">40</option><option value="41">41</option><option value="42">42</option><option value="43">43</option><option value="44">44</option><option value="45">45</option><option value="46">46</option><option value="47">47</option><option value="48">48</option><option value="49">49</option><option value="50">50</option><option value="51">51</option><option value="52">52</option><option value="53">53</option><option value="54">54</option><option value="55">55</option><option value="56">56</option><option value="57">57</option><option value="58">58</option><option value="59">59</option><option value="60">60</option><option value="61">61</option><option value="62">62</option><option value="63">63</option><option value="64">64</option><option value="65">65</option><option value="66">66</option><option value="67">67</option><option value="68">68</option><option value="69">69</option><option value="70">70</option><option value="71">71</option><option value="72">72</option><option value="73">73</option><option value="74">74</option><option value="75">75</option><option value="76">76</option><option value="77">77</option><option value="78">78</option><option value="79">79</option><option value="80">80</option><option value="81">81</option><option value="82">82</option><option value="83">83</option><option value="84">84</option><option value="85">85</option><option value="86">86</option><option value="87">87</option><option value="88">88</option><option value="89">89</option><option value="90">90</option><option value="91">91</option><option value="92">92</option><option value="93">93</option><option value="94">94</option><option value="95">95</option><option value="96">96</option><option value="97">97</option><option value="98">98</option><option value="99">99</option><option value="100">100</option></select></span></td>
				<td><span class="wpcf7-form-control-wrap">
				<span style="position: relative; bottom: 7px;">
				<div style="width: 100px;overflow: hidden;" id="frontPrintNtext">Not Selected</div>
				<a class="dialog" onclick="SetVar('frontPrintN');" href="#frontPrintN">Select</a>
				<span style="display: none;" id="frontPrintNremove">- <a onclick="remove1('frontPrintN','N');" href="#frontPrintN">Remove</a></span>
				<input type="hidden" name="frontPrintN" id="frontPrintN">
                                    <input value="0" type="hidden" name="frontJumboN" id="frontJumboN">
                                        <input value="0" type="hidden" name="frontUnderbaseN" id="frontUnderbaseN">
                                    
				</span>
				</span></td>
				<td><span class="wpcf7-form-control-wrap">
				<span style="position: relative; bottom: 7px;">
				<div style="width: 100px;overflow: hidden;" id="frontMockupNtext">Not Selected</div>
				<a class="dialog" onclick="SetVar('frontMockupN');" href="#frontMockupN">Select</a>
				<span style="display: none;" id="frontMockupNremove">- <a onclick="remove1('frontMockupN','N');" href="#frontMockupN">Remove</a></span>
				<input type="hidden" name="frontMockupN" id="frontMockupN">
				</span></span></td>
				<td><span class="wpcf7-form-control-wrap" id="BackPrintCellN">
				<span style="position: relative; bottom: 7px;">
				<div style="width: 100px;overflow: hidden;" id="backPrintNtext">Not Selected</div>
				<a class="dialog" onclick="SetVar('backPrintN');" href="#backPrintN">Select</a>
				<span style="display: none;" id="backPrintNremove">- <a onclick="remove1('backPrintN','N');" href="#backPrintN">Remove</a></span>
				<input type="hidden" name="backPrintN" id="backPrintN">
                                <input value="0" type="hidden" name="backJumboN" id="backJumboN">
                                    <input value="0" type="hidden" name="backUnderbaseN" id="backUnderbaseN">
			 	</span></span></td>
				<td><span class="wpcf7-form-control-wrap" id="BackMockupCellN">
				<span style="position: relative; bottom: 7px;">
				<div style="width: 100px;overflow: hidden;" id="backMockupNtext">Not Selected</div>
				<a class="dialog" onclick="SetVar('backMockupN');" href="#backMockupN">Select</a>
				<span style="display: none;" id="backMockupNremove">- <a onclick="remove1('backMockupN','N');" href="#backMockupN">Remove</a></span>
				<input type="hidden" name="backMockupN" id="backMockupN">
                                    
			 </span> </span></td>
				<td><input type="text" style="width:50px;" name="CostN" value="0.00" id="CostN" readonly="">
				<input type="hidden" name="PrintPriceN" value="0" id="PrintPriceN">
                                    
				</td>
				</tr>
				<tr id="holderO" style="display:none;">
				<td><span class="wpcf7-form-control-wrap"><select class="inventory_brand" name="BrandO" id="BrandO" style="width:100px;">
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
<input name="shipping_typeO" id="shipping_typeO" style="display:none;"></span></td><td><span class="wpcf7-form-control-wrap"><input type="hidden" value="1" id="rushedO"><select name="NameO" id="NameO" style="width:100px;"><option value="">Select</option></select></span></td><td><span class="wpcf7-form-control-wrap"><br><select name="ColorO" id="ColorO" style="width:90px;"><option value="">Select</option></select></span></td><td><span class="wpcf7-form-control-wrap"><br><select name="SizeO" id="SizeO" style="width:90px;"><option value="">Select</option></select></span></td><td><span class="wpcf7-form-control-wrap"><br><select name="QuantityO" id="QuantityO" style="width:50px;"><option value="1">1</option><option value="2">2</option><option value="3">3</option><option value="4">4</option><option value="5">5</option><option value="6">6</option><option value="7">7</option><option value="8">8</option><option value="9">9</option><option value="10">10</option><option value="11">11</option><option value="12">12</option><option value="13">13</option><option value="14">14</option><option value="15">15</option><option value="16">16</option><option value="17">17</option><option value="18">18</option><option value="19">19</option><option value="20">20</option><option value="21">21</option><option value="22">22</option><option value="23">23</option><option value="24">24</option><option value="25">25</option><option value="26">26</option><option value="27">27</option><option value="28">28</option><option value="29">29</option><option value="30">30</option><option value="31">31</option><option value="32">32</option><option value="33">33</option><option value="34">34</option><option value="35">35</option><option value="36">36</option><option value="37">37</option><option value="38">38</option><option value="39">39</option><option value="40">40</option><option value="41">41</option><option value="42">42</option><option value="43">43</option><option value="44">44</option><option value="45">45</option><option value="46">46</option><option value="47">47</option><option value="48">48</option><option value="49">49</option><option value="50">50</option><option value="51">51</option><option value="52">52</option><option value="53">53</option><option value="54">54</option><option value="55">55</option><option value="56">56</option><option value="57">57</option><option value="58">58</option><option value="59">59</option><option value="60">60</option><option value="61">61</option><option value="62">62</option><option value="63">63</option><option value="64">64</option><option value="65">65</option><option value="66">66</option><option value="67">67</option><option value="68">68</option><option value="69">69</option><option value="70">70</option><option value="71">71</option><option value="72">72</option><option value="73">73</option><option value="74">74</option><option value="75">75</option><option value="76">76</option><option value="77">77</option><option value="78">78</option><option value="79">79</option><option value="80">80</option><option value="81">81</option><option value="82">82</option><option value="83">83</option><option value="84">84</option><option value="85">85</option><option value="86">86</option><option value="87">87</option><option value="88">88</option><option value="89">89</option><option value="90">90</option><option value="91">91</option><option value="92">92</option><option value="93">93</option><option value="94">94</option><option value="95">95</option><option value="96">96</option><option value="97">97</option><option value="98">98</option><option value="99">99</option><option value="100">100</option></select></span></td>
				<td><span class="wpcf7-form-control-wrap">
				<span style="position: relative; bottom: 7px;">
				<div style="width: 100px;overflow: hidden;" id="frontPrintOtext">Not Selected</div>
				<a class="dialog" onclick="SetVar('frontPrintO');" href="#frontPrintO">Select</a>
				<span style="display: none;" id="frontPrintOremove">- <a onclick="remove1('frontPrintO','O');" href="#frontPrintO">Remove</a></span>
				<input type="hidden" name="frontPrintO" id="frontPrintO">
                                    <input value="0" type="hidden" name="frontJumboO" id="frontJumboO">
                                        <input value="0" type="hidden" name="frontUnderbaseO" id="frontUnderbaseO">
                                    
				</span>
				</span></td>
				<td><span class="wpcf7-form-control-wrap">
				<span style="position: relative; bottom: 7px;">
				<div style="width: 100px;overflow: hidden;" id="frontMockupOtext">Not Selected</div>
				<a class="dialog" onclick="SetVar('frontMockupO');" href="#frontMockupO">Select</a>
				<span style="display: none;" id="frontMockupOremove">- <a onclick="remove1('frontMockupO','O');" href="#frontMockupO">Remove</a></span>
				<input type="hidden" name="frontMockupO" id="frontMockupO">
				</span></span></td>
				<td><span class="wpcf7-form-control-wrap" id="BackPrintCellO">
				<span style="position: relative; bottom: 7px;">
				<div style="width: 100px;overflow: hidden;" id="backPrintOtext">Not Selected</div>
				<a class="dialog" onclick="SetVar('backPrintO');" href="#backPrintO">Select</a>
				<span style="display: none;" id="backPrintOremove">- <a onclick="remove1('backPrintO','O');" href="#backPrintO">Remove</a></span>
				<input type="hidden" name="backPrintO" id="backPrintO">
                                <input value="0" type="hidden" name="backJumboO" id="backJumboO">
                                    <input value="0" type="hidden" name="backUnderbaseO" id="backUnderbaseO">
			 	</span></span></td>
				<td><span class="wpcf7-form-control-wrap" id="BackMockupCellO">
				<span style="position: relative; bottom: 7px;">
				<div style="width: 100px;overflow: hidden;" id="backMockupOtext">Not Selected</div>
				<a class="dialog" onclick="SetVar('backMockupO');" href="#backMockupO">Select</a>
				<span style="display: none;" id="backMockupOremove">- <a onclick="remove1('backMockupO','O');" href="#backMockupO">Remove</a></span>
				<input type="hidden" name="backMockupO" id="backMockupO">
                                    
			 </span> </span></td>
				<td><input type="text" style="width:50px;" name="CostO" value="0.00" id="CostO" readonly="">
				<input type="hidden" name="PrintPriceO" value="0" id="PrintPriceO">
                                    
				</td>
				</tr>
				<tr id="holderP" style="display:none;">
				<td><span class="wpcf7-form-control-wrap"><select class="inventory_brand" name="BrandP" id="BrandP" style="width:100px;">
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
<input name="shipping_typeP" id="shipping_typeP" style="display:none;"></span></td><td><span class="wpcf7-form-control-wrap"><input type="hidden" value="1" id="rushedP"><select name="NameP" id="NameP" style="width:100px;"><option value="">Select</option></select></span></td><td><span class="wpcf7-form-control-wrap"><br><select name="ColorP" id="ColorP" style="width:90px;"><option value="">Select</option></select></span></td><td><span class="wpcf7-form-control-wrap"><br><select name="SizeP" id="SizeP" style="width:90px;"><option value="">Select</option></select></span></td><td><span class="wpcf7-form-control-wrap"><br><select name="QuantityP" id="QuantityP" style="width:50px;"><option value="1">1</option><option value="2">2</option><option value="3">3</option><option value="4">4</option><option value="5">5</option><option value="6">6</option><option value="7">7</option><option value="8">8</option><option value="9">9</option><option value="10">10</option><option value="11">11</option><option value="12">12</option><option value="13">13</option><option value="14">14</option><option value="15">15</option><option value="16">16</option><option value="17">17</option><option value="18">18</option><option value="19">19</option><option value="20">20</option><option value="21">21</option><option value="22">22</option><option value="23">23</option><option value="24">24</option><option value="25">25</option><option value="26">26</option><option value="27">27</option><option value="28">28</option><option value="29">29</option><option value="30">30</option><option value="31">31</option><option value="32">32</option><option value="33">33</option><option value="34">34</option><option value="35">35</option><option value="36">36</option><option value="37">37</option><option value="38">38</option><option value="39">39</option><option value="40">40</option><option value="41">41</option><option value="42">42</option><option value="43">43</option><option value="44">44</option><option value="45">45</option><option value="46">46</option><option value="47">47</option><option value="48">48</option><option value="49">49</option><option value="50">50</option><option value="51">51</option><option value="52">52</option><option value="53">53</option><option value="54">54</option><option value="55">55</option><option value="56">56</option><option value="57">57</option><option value="58">58</option><option value="59">59</option><option value="60">60</option><option value="61">61</option><option value="62">62</option><option value="63">63</option><option value="64">64</option><option value="65">65</option><option value="66">66</option><option value="67">67</option><option value="68">68</option><option value="69">69</option><option value="70">70</option><option value="71">71</option><option value="72">72</option><option value="73">73</option><option value="74">74</option><option value="75">75</option><option value="76">76</option><option value="77">77</option><option value="78">78</option><option value="79">79</option><option value="80">80</option><option value="81">81</option><option value="82">82</option><option value="83">83</option><option value="84">84</option><option value="85">85</option><option value="86">86</option><option value="87">87</option><option value="88">88</option><option value="89">89</option><option value="90">90</option><option value="91">91</option><option value="92">92</option><option value="93">93</option><option value="94">94</option><option value="95">95</option><option value="96">96</option><option value="97">97</option><option value="98">98</option><option value="99">99</option><option value="100">100</option></select></span></td>
				<td><span class="wpcf7-form-control-wrap">
				<span style="position: relative; bottom: 7px;">
				<div style="width: 100px;overflow: hidden;" id="frontPrintPtext">Not Selected</div>
				<a class="dialog" onclick="SetVar('frontPrintP');" href="#frontPrintP">Select</a>
				<span style="display: none;" id="frontPrintPremove">- <a onclick="remove1('frontPrintP','P');" href="#frontPrintP">Remove</a></span>
				<input type="hidden" name="frontPrintP" id="frontPrintP">
                                    <input value="0" type="hidden" name="frontJumboP" id="frontJumboP">
                                        <input value="0" type="hidden" name="frontUnderbaseP" id="frontUnderbaseP">
                                    
				</span>
				</span></td>
				<td><span class="wpcf7-form-control-wrap">
				<span style="position: relative; bottom: 7px;">
				<div style="width: 100px;overflow: hidden;" id="frontMockupPtext">Not Selected</div>
				<a class="dialog" onclick="SetVar('frontMockupP');" href="#frontMockupP">Select</a>
				<span style="display: none;" id="frontMockupPremove">- <a onclick="remove1('frontMockupP','P');" href="#frontMockupP">Remove</a></span>
				<input type="hidden" name="frontMockupP" id="frontMockupP">
				</span></span></td>
				<td><span class="wpcf7-form-control-wrap" id="BackPrintCellP">
				<span style="position: relative; bottom: 7px;">
				<div style="width: 100px;overflow: hidden;" id="backPrintPtext">Not Selected</div>
				<a class="dialog" onclick="SetVar('backPrintP');" href="#backPrintP">Select</a>
				<span style="display: none;" id="backPrintPremove">- <a onclick="remove1('backPrintP','P');" href="#backPrintP">Remove</a></span>
				<input type="hidden" name="backPrintP" id="backPrintP">
                                <input value="0" type="hidden" name="backJumboP" id="backJumboP">
                                    <input value="0" type="hidden" name="backUnderbaseP" id="backUnderbaseP">
			 	</span></span></td>
				<td><span class="wpcf7-form-control-wrap" id="BackMockupCellP">
				<span style="position: relative; bottom: 7px;">
				<div style="width: 100px;overflow: hidden;" id="backMockupPtext">Not Selected</div>
				<a class="dialog" onclick="SetVar('backMockupP');" href="#backMockupP">Select</a>
				<span style="display: none;" id="backMockupPremove">- <a onclick="remove1('backMockupP','P');" href="#backMockupP">Remove</a></span>
				<input type="hidden" name="backMockupP" id="backMockupP">
                                    
			 </span> </span></td>
				<td><input type="text" style="width:50px;" name="CostP" value="0.00" id="CostP" readonly="">
				<input type="hidden" name="PrintPriceP" value="0" id="PrintPriceP">
                                    
				</td>
				</tr>
				<tr id="holderQ" style="display:none;">
				<td><span class="wpcf7-form-control-wrap"><select class="inventory_brand" name="BrandQ" id="BrandQ" style="width:100px;">
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
<input name="shipping_typeQ" id="shipping_typeQ" style="display:none;"></span></td><td><span class="wpcf7-form-control-wrap"><input type="hidden" value="1" id="rushedQ"><select name="NameQ" id="NameQ" style="width:100px;"><option value="">Select</option></select></span></td><td><span class="wpcf7-form-control-wrap"><br><select name="ColorQ" id="ColorQ" style="width:90px;"><option value="">Select</option></select></span></td><td><span class="wpcf7-form-control-wrap"><br><select name="SizeQ" id="SizeQ" style="width:90px;"><option value="">Select</option></select></span></td><td><span class="wpcf7-form-control-wrap"><br><select name="QuantityQ" id="QuantityQ" style="width:50px;"><option value="1">1</option><option value="2">2</option><option value="3">3</option><option value="4">4</option><option value="5">5</option><option value="6">6</option><option value="7">7</option><option value="8">8</option><option value="9">9</option><option value="10">10</option><option value="11">11</option><option value="12">12</option><option value="13">13</option><option value="14">14</option><option value="15">15</option><option value="16">16</option><option value="17">17</option><option value="18">18</option><option value="19">19</option><option value="20">20</option><option value="21">21</option><option value="22">22</option><option value="23">23</option><option value="24">24</option><option value="25">25</option><option value="26">26</option><option value="27">27</option><option value="28">28</option><option value="29">29</option><option value="30">30</option><option value="31">31</option><option value="32">32</option><option value="33">33</option><option value="34">34</option><option value="35">35</option><option value="36">36</option><option value="37">37</option><option value="38">38</option><option value="39">39</option><option value="40">40</option><option value="41">41</option><option value="42">42</option><option value="43">43</option><option value="44">44</option><option value="45">45</option><option value="46">46</option><option value="47">47</option><option value="48">48</option><option value="49">49</option><option value="50">50</option><option value="51">51</option><option value="52">52</option><option value="53">53</option><option value="54">54</option><option value="55">55</option><option value="56">56</option><option value="57">57</option><option value="58">58</option><option value="59">59</option><option value="60">60</option><option value="61">61</option><option value="62">62</option><option value="63">63</option><option value="64">64</option><option value="65">65</option><option value="66">66</option><option value="67">67</option><option value="68">68</option><option value="69">69</option><option value="70">70</option><option value="71">71</option><option value="72">72</option><option value="73">73</option><option value="74">74</option><option value="75">75</option><option value="76">76</option><option value="77">77</option><option value="78">78</option><option value="79">79</option><option value="80">80</option><option value="81">81</option><option value="82">82</option><option value="83">83</option><option value="84">84</option><option value="85">85</option><option value="86">86</option><option value="87">87</option><option value="88">88</option><option value="89">89</option><option value="90">90</option><option value="91">91</option><option value="92">92</option><option value="93">93</option><option value="94">94</option><option value="95">95</option><option value="96">96</option><option value="97">97</option><option value="98">98</option><option value="99">99</option><option value="100">100</option></select></span></td>
				<td><span class="wpcf7-form-control-wrap">
				<span style="position: relative; bottom: 7px;">
				<div style="width: 100px;overflow: hidden;" id="frontPrintQtext">Not Selected</div>
				<a class="dialog" onclick="SetVar('frontPrintQ');" href="#frontPrintQ">Select</a>
				<span style="display: none;" id="frontPrintQremove">- <a onclick="remove1('frontPrintQ','Q');" href="#frontPrintQ">Remove</a></span>
				<input type="hidden" name="frontPrintQ" id="frontPrintQ">
                                    <input value="0" type="hidden" name="frontJumboQ" id="frontJumboQ">
                                        <input value="0" type="hidden" name="frontUnderbaseQ" id="frontUnderbaseQ">
                                    
				</span>
				</span></td>
				<td><span class="wpcf7-form-control-wrap">
				<span style="position: relative; bottom: 7px;">
				<div style="width: 100px;overflow: hidden;" id="frontMockupQtext">Not Selected</div>
				<a class="dialog" onclick="SetVar('frontMockupQ');" href="#frontMockupQ">Select</a>
				<span style="display: none;" id="frontMockupQremove">- <a onclick="remove1('frontMockupQ','Q');" href="#frontMockupQ">Remove</a></span>
				<input type="hidden" name="frontMockupQ" id="frontMockupQ">
				</span></span></td>
				<td><span class="wpcf7-form-control-wrap" id="BackPrintCellQ">
				<span style="position: relative; bottom: 7px;">
				<div style="width: 100px;overflow: hidden;" id="backPrintQtext">Not Selected</div>
				<a class="dialog" onclick="SetVar('backPrintQ');" href="#backPrintQ">Select</a>
				<span style="display: none;" id="backPrintQremove">- <a onclick="remove1('backPrintQ','Q');" href="#backPrintQ">Remove</a></span>
				<input type="hidden" name="backPrintQ" id="backPrintQ">
                                <input value="0" type="hidden" name="backJumboQ" id="backJumboQ">
                                    <input value="0" type="hidden" name="backUnderbaseQ" id="backUnderbaseQ">
			 	</span></span></td>
				<td><span class="wpcf7-form-control-wrap" id="BackMockupCellQ">
				<span style="position: relative; bottom: 7px;">
				<div style="width: 100px;overflow: hidden;" id="backMockupQtext">Not Selected</div>
				<a class="dialog" onclick="SetVar('backMockupQ');" href="#backMockupQ">Select</a>
				<span style="display: none;" id="backMockupQremove">- <a onclick="remove1('backMockupQ','Q');" href="#backMockupQ">Remove</a></span>
				<input type="hidden" name="backMockupQ" id="backMockupQ">
                                    
			 </span> </span></td>
				<td><input type="text" style="width:50px;" name="CostQ" value="0.00" id="CostQ" readonly="">
				<input type="hidden" name="PrintPriceQ" value="0" id="PrintPriceQ">
                                    
				</td>
				</tr>
				<tr id="holderR" style="display:none;">
				<td><span class="wpcf7-form-control-wrap"><select class="inventory_brand" name="BrandR" id="BrandR" style="width:100px;">
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
<input name="shipping_typeR" id="shipping_typeR" style="display:none;"></span></td><td><span class="wpcf7-form-control-wrap"><input type="hidden" value="1" id="rushedR"><select name="NameR" id="NameR" style="width:100px;"><option value="">Select</option></select></span></td><td><span class="wpcf7-form-control-wrap"><br><select name="ColorR" id="ColorR" style="width:90px;"><option value="">Select</option></select></span></td><td><span class="wpcf7-form-control-wrap"><br><select name="SizeR" id="SizeR" style="width:90px;"><option value="">Select</option></select></span></td><td><span class="wpcf7-form-control-wrap"><br><select name="QuantityR" id="QuantityR" style="width:50px;"><option value="1">1</option><option value="2">2</option><option value="3">3</option><option value="4">4</option><option value="5">5</option><option value="6">6</option><option value="7">7</option><option value="8">8</option><option value="9">9</option><option value="10">10</option><option value="11">11</option><option value="12">12</option><option value="13">13</option><option value="14">14</option><option value="15">15</option><option value="16">16</option><option value="17">17</option><option value="18">18</option><option value="19">19</option><option value="20">20</option><option value="21">21</option><option value="22">22</option><option value="23">23</option><option value="24">24</option><option value="25">25</option><option value="26">26</option><option value="27">27</option><option value="28">28</option><option value="29">29</option><option value="30">30</option><option value="31">31</option><option value="32">32</option><option value="33">33</option><option value="34">34</option><option value="35">35</option><option value="36">36</option><option value="37">37</option><option value="38">38</option><option value="39">39</option><option value="40">40</option><option value="41">41</option><option value="42">42</option><option value="43">43</option><option value="44">44</option><option value="45">45</option><option value="46">46</option><option value="47">47</option><option value="48">48</option><option value="49">49</option><option value="50">50</option><option value="51">51</option><option value="52">52</option><option value="53">53</option><option value="54">54</option><option value="55">55</option><option value="56">56</option><option value="57">57</option><option value="58">58</option><option value="59">59</option><option value="60">60</option><option value="61">61</option><option value="62">62</option><option value="63">63</option><option value="64">64</option><option value="65">65</option><option value="66">66</option><option value="67">67</option><option value="68">68</option><option value="69">69</option><option value="70">70</option><option value="71">71</option><option value="72">72</option><option value="73">73</option><option value="74">74</option><option value="75">75</option><option value="76">76</option><option value="77">77</option><option value="78">78</option><option value="79">79</option><option value="80">80</option><option value="81">81</option><option value="82">82</option><option value="83">83</option><option value="84">84</option><option value="85">85</option><option value="86">86</option><option value="87">87</option><option value="88">88</option><option value="89">89</option><option value="90">90</option><option value="91">91</option><option value="92">92</option><option value="93">93</option><option value="94">94</option><option value="95">95</option><option value="96">96</option><option value="97">97</option><option value="98">98</option><option value="99">99</option><option value="100">100</option></select></span></td>
				<td><span class="wpcf7-form-control-wrap">
				<span style="position: relative; bottom: 7px;">
				<div style="width: 100px;overflow: hidden;" id="frontPrintRtext">Not Selected</div>
				<a class="dialog" onclick="SetVar('frontPrintR');" href="#frontPrintR">Select</a>
				<span style="display: none;" id="frontPrintRremove">- <a onclick="remove1('frontPrintR','R');" href="#frontPrintR">Remove</a></span>
				<input type="hidden" name="frontPrintR" id="frontPrintR">
                                    <input value="0" type="hidden" name="frontJumboR" id="frontJumboR">
                                        <input value="0" type="hidden" name="frontUnderbaseR" id="frontUnderbaseR">
                                    
				</span>
				</span></td>
				<td><span class="wpcf7-form-control-wrap">
				<span style="position: relative; bottom: 7px;">
				<div style="width: 100px;overflow: hidden;" id="frontMockupRtext">Not Selected</div>
				<a class="dialog" onclick="SetVar('frontMockupR');" href="#frontMockupR">Select</a>
				<span style="display: none;" id="frontMockupRremove">- <a onclick="remove1('frontMockupR','R');" href="#frontMockupR">Remove</a></span>
				<input type="hidden" name="frontMockupR" id="frontMockupR">
				</span></span></td>
				<td><span class="wpcf7-form-control-wrap" id="BackPrintCellR">
				<span style="position: relative; bottom: 7px;">
				<div style="width: 100px;overflow: hidden;" id="backPrintRtext">Not Selected</div>
				<a class="dialog" onclick="SetVar('backPrintR');" href="#backPrintR">Select</a>
				<span style="display: none;" id="backPrintRremove">- <a onclick="remove1('backPrintR','R');" href="#backPrintR">Remove</a></span>
				<input type="hidden" name="backPrintR" id="backPrintR">
                                <input value="0" type="hidden" name="backJumboR" id="backJumboR">
                                    <input value="0" type="hidden" name="backUnderbaseR" id="backUnderbaseR">
			 	</span></span></td>
				<td><span class="wpcf7-form-control-wrap" id="BackMockupCellR">
				<span style="position: relative; bottom: 7px;">
				<div style="width: 100px;overflow: hidden;" id="backMockupRtext">Not Selected</div>
				<a class="dialog" onclick="SetVar('backMockupR');" href="#backMockupR">Select</a>
				<span style="display: none;" id="backMockupRremove">- <a onclick="remove1('backMockupR','R');" href="#backMockupR">Remove</a></span>
				<input type="hidden" name="backMockupR" id="backMockupR">
                                    
			 </span> </span></td>
				<td><input type="text" style="width:50px;" name="CostR" value="0.00" id="CostR" readonly="">
				<input type="hidden" name="PrintPriceR" value="0" id="PrintPriceR">
                                    
				</td>
				</tr>
				<tr id="holderS" style="display:none;">
				<td><span class="wpcf7-form-control-wrap"><select class="inventory_brand" name="BrandS" id="BrandS" style="width:100px;">
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
<input name="shipping_typeS" id="shipping_typeS" style="display:none;"></span></td><td><span class="wpcf7-form-control-wrap"><input type="hidden" value="1" id="rushedS"><select name="NameS" id="NameS" style="width:100px;"><option value="">Select</option></select></span></td><td><span class="wpcf7-form-control-wrap"><br><select name="ColorS" id="ColorS" style="width:90px;"><option value="">Select</option></select></span></td><td><span class="wpcf7-form-control-wrap"><br><select name="SizeS" id="SizeS" style="width:90px;"><option value="">Select</option></select></span></td><td><span class="wpcf7-form-control-wrap"><br><select name="QuantityS" id="QuantityS" style="width:50px;"><option value="1">1</option><option value="2">2</option><option value="3">3</option><option value="4">4</option><option value="5">5</option><option value="6">6</option><option value="7">7</option><option value="8">8</option><option value="9">9</option><option value="10">10</option><option value="11">11</option><option value="12">12</option><option value="13">13</option><option value="14">14</option><option value="15">15</option><option value="16">16</option><option value="17">17</option><option value="18">18</option><option value="19">19</option><option value="20">20</option><option value="21">21</option><option value="22">22</option><option value="23">23</option><option value="24">24</option><option value="25">25</option><option value="26">26</option><option value="27">27</option><option value="28">28</option><option value="29">29</option><option value="30">30</option><option value="31">31</option><option value="32">32</option><option value="33">33</option><option value="34">34</option><option value="35">35</option><option value="36">36</option><option value="37">37</option><option value="38">38</option><option value="39">39</option><option value="40">40</option><option value="41">41</option><option value="42">42</option><option value="43">43</option><option value="44">44</option><option value="45">45</option><option value="46">46</option><option value="47">47</option><option value="48">48</option><option value="49">49</option><option value="50">50</option><option value="51">51</option><option value="52">52</option><option value="53">53</option><option value="54">54</option><option value="55">55</option><option value="56">56</option><option value="57">57</option><option value="58">58</option><option value="59">59</option><option value="60">60</option><option value="61">61</option><option value="62">62</option><option value="63">63</option><option value="64">64</option><option value="65">65</option><option value="66">66</option><option value="67">67</option><option value="68">68</option><option value="69">69</option><option value="70">70</option><option value="71">71</option><option value="72">72</option><option value="73">73</option><option value="74">74</option><option value="75">75</option><option value="76">76</option><option value="77">77</option><option value="78">78</option><option value="79">79</option><option value="80">80</option><option value="81">81</option><option value="82">82</option><option value="83">83</option><option value="84">84</option><option value="85">85</option><option value="86">86</option><option value="87">87</option><option value="88">88</option><option value="89">89</option><option value="90">90</option><option value="91">91</option><option value="92">92</option><option value="93">93</option><option value="94">94</option><option value="95">95</option><option value="96">96</option><option value="97">97</option><option value="98">98</option><option value="99">99</option><option value="100">100</option></select></span></td>
				<td><span class="wpcf7-form-control-wrap">
				<span style="position: relative; bottom: 7px;">
				<div style="width: 100px;overflow: hidden;" id="frontPrintStext">Not Selected</div>
				<a class="dialog" onclick="SetVar('frontPrintS');" href="#frontPrintS">Select</a>
				<span style="display: none;" id="frontPrintSremove">- <a onclick="remove1('frontPrintS','S');" href="#frontPrintS">Remove</a></span>
				<input type="hidden" name="frontPrintS" id="frontPrintS">
                                    <input value="0" type="hidden" name="frontJumboS" id="frontJumboS">
                                        <input value="0" type="hidden" name="frontUnderbaseS" id="frontUnderbaseS">
                                    
				</span>
				</span></td>
				<td><span class="wpcf7-form-control-wrap">
				<span style="position: relative; bottom: 7px;">
				<div style="width: 100px;overflow: hidden;" id="frontMockupStext">Not Selected</div>
				<a class="dialog" onclick="SetVar('frontMockupS');" href="#frontMockupS">Select</a>
				<span style="display: none;" id="frontMockupSremove">- <a onclick="remove1('frontMockupS','S');" href="#frontMockupS">Remove</a></span>
				<input type="hidden" name="frontMockupS" id="frontMockupS">
				</span></span></td>
				<td><span class="wpcf7-form-control-wrap" id="BackPrintCellS">
				<span style="position: relative; bottom: 7px;">
				<div style="width: 100px;overflow: hidden;" id="backPrintStext">Not Selected</div>
				<a class="dialog" onclick="SetVar('backPrintS');" href="#backPrintS">Select</a>
				<span style="display: none;" id="backPrintSremove">- <a onclick="remove1('backPrintS','S');" href="#backPrintS">Remove</a></span>
				<input type="hidden" name="backPrintS" id="backPrintS">
                                <input value="0" type="hidden" name="backJumboS" id="backJumboS">
                                    <input value="0" type="hidden" name="backUnderbaseS" id="backUnderbaseS">
			 	</span></span></td>
				<td><span class="wpcf7-form-control-wrap" id="BackMockupCellS">
				<span style="position: relative; bottom: 7px;">
				<div style="width: 100px;overflow: hidden;" id="backMockupStext">Not Selected</div>
				<a class="dialog" onclick="SetVar('backMockupS');" href="#backMockupS">Select</a>
				<span style="display: none;" id="backMockupSremove">- <a onclick="remove1('backMockupS','S');" href="#backMockupS">Remove</a></span>
				<input type="hidden" name="backMockupS" id="backMockupS">
                                    
			 </span> </span></td>
				<td><input type="text" style="width:50px;" name="CostS" value="0.00" id="CostS" readonly="">
				<input type="hidden" name="PrintPriceS" value="0" id="PrintPriceS">
                                    
				</td>
				</tr>
				<tr id="holderT" style="display:none;">
				<td><span class="wpcf7-form-control-wrap"><select class="inventory_brand" name="BrandT" id="BrandT" style="width:100px;">
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
<input name="shipping_typeT" id="shipping_typeT" style="display:none;"></span></td><td><span class="wpcf7-form-control-wrap"><input type="hidden" value="1" id="rushedT"><select name="NameT" id="NameT" style="width:100px;"><option value="">Select</option></select></span></td><td><span class="wpcf7-form-control-wrap"><br><select name="ColorT" id="ColorT" style="width:90px;"><option value="">Select</option></select></span></td><td><span class="wpcf7-form-control-wrap"><br><select name="SizeT" id="SizeT" style="width:90px;"><option value="">Select</option></select></span></td><td><span class="wpcf7-form-control-wrap"><br><select name="QuantityT" id="QuantityT" style="width:50px;"><option value="1">1</option><option value="2">2</option><option value="3">3</option><option value="4">4</option><option value="5">5</option><option value="6">6</option><option value="7">7</option><option value="8">8</option><option value="9">9</option><option value="10">10</option><option value="11">11</option><option value="12">12</option><option value="13">13</option><option value="14">14</option><option value="15">15</option><option value="16">16</option><option value="17">17</option><option value="18">18</option><option value="19">19</option><option value="20">20</option><option value="21">21</option><option value="22">22</option><option value="23">23</option><option value="24">24</option><option value="25">25</option><option value="26">26</option><option value="27">27</option><option value="28">28</option><option value="29">29</option><option value="30">30</option><option value="31">31</option><option value="32">32</option><option value="33">33</option><option value="34">34</option><option value="35">35</option><option value="36">36</option><option value="37">37</option><option value="38">38</option><option value="39">39</option><option value="40">40</option><option value="41">41</option><option value="42">42</option><option value="43">43</option><option value="44">44</option><option value="45">45</option><option value="46">46</option><option value="47">47</option><option value="48">48</option><option value="49">49</option><option value="50">50</option><option value="51">51</option><option value="52">52</option><option value="53">53</option><option value="54">54</option><option value="55">55</option><option value="56">56</option><option value="57">57</option><option value="58">58</option><option value="59">59</option><option value="60">60</option><option value="61">61</option><option value="62">62</option><option value="63">63</option><option value="64">64</option><option value="65">65</option><option value="66">66</option><option value="67">67</option><option value="68">68</option><option value="69">69</option><option value="70">70</option><option value="71">71</option><option value="72">72</option><option value="73">73</option><option value="74">74</option><option value="75">75</option><option value="76">76</option><option value="77">77</option><option value="78">78</option><option value="79">79</option><option value="80">80</option><option value="81">81</option><option value="82">82</option><option value="83">83</option><option value="84">84</option><option value="85">85</option><option value="86">86</option><option value="87">87</option><option value="88">88</option><option value="89">89</option><option value="90">90</option><option value="91">91</option><option value="92">92</option><option value="93">93</option><option value="94">94</option><option value="95">95</option><option value="96">96</option><option value="97">97</option><option value="98">98</option><option value="99">99</option><option value="100">100</option></select></span></td>
				<td><span class="wpcf7-form-control-wrap">
				<span style="position: relative; bottom: 7px;">
				<div style="width: 100px;overflow: hidden;" id="frontPrintTtext">Not Selected</div>
				<a class="dialog" onclick="SetVar('frontPrintT');" href="#frontPrintT">Select</a>
				<span style="display: none;" id="frontPrintTremove">- <a onclick="remove1('frontPrintT','T');" href="#frontPrintT">Remove</a></span>
				<input type="hidden" name="frontPrintT" id="frontPrintT">
                                    <input value="0" type="hidden" name="frontJumboT" id="frontJumboT">
                                        <input value="0" type="hidden" name="frontUnderbaseT" id="frontUnderbaseT">
                                    
				</span>
				</span></td>
				<td><span class="wpcf7-form-control-wrap">
				<span style="position: relative; bottom: 7px;">
				<div style="width: 100px;overflow: hidden;" id="frontMockupTtext">Not Selected</div>
				<a class="dialog" onclick="SetVar('frontMockupT');" href="#frontMockupT">Select</a>
				<span style="display: none;" id="frontMockupTremove">- <a onclick="remove1('frontMockupT','T');" href="#frontMockupT">Remove</a></span>
				<input type="hidden" name="frontMockupT" id="frontMockupT">
				</span></span></td>
				<td><span class="wpcf7-form-control-wrap" id="BackPrintCellT">
				<span style="position: relative; bottom: 7px;">
				<div style="width: 100px;overflow: hidden;" id="backPrintTtext">Not Selected</div>
				<a class="dialog" onclick="SetVar('backPrintT');" href="#backPrintT">Select</a>
				<span style="display: none;" id="backPrintTremove">- <a onclick="remove1('backPrintT','T');" href="#backPrintT">Remove</a></span>
				<input type="hidden" name="backPrintT" id="backPrintT">
                                <input value="0" type="hidden" name="backJumboT" id="backJumboT">
                                    <input value="0" type="hidden" name="backUnderbaseT" id="backUnderbaseT">
			 	</span></span></td>
				<td><span class="wpcf7-form-control-wrap" id="BackMockupCellT">
				<span style="position: relative; bottom: 7px;">
				<div style="width: 100px;overflow: hidden;" id="backMockupTtext">Not Selected</div>
				<a class="dialog" onclick="SetVar('backMockupT');" href="#backMockupT">Select</a>
				<span style="display: none;" id="backMockupTremove">- <a onclick="remove1('backMockupT','T');" href="#backMockupT">Remove</a></span>
				<input type="hidden" name="backMockupT" id="backMockupT">
                                    
			 </span> </span></td>
				<td><input type="text" style="width:50px;" name="CostT" value="0.00" id="CostT" readonly="">
				<input type="hidden" name="PrintPriceT" value="0" id="PrintPriceT">
                                    
				</td>
				</tr>
				<tr id="holderU" style="display:none;">
				<td><span class="wpcf7-form-control-wrap"><select class="inventory_brand" name="BrandU" id="BrandU" style="width:100px;">
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
<input name="shipping_typeU" id="shipping_typeU" style="display:none;"></span></td><td><span class="wpcf7-form-control-wrap"><input type="hidden" value="1" id="rushedU"><select name="NameU" id="NameU" style="width:100px;"><option value="">Select</option></select></span></td><td><span class="wpcf7-form-control-wrap"><br><select name="ColorU" id="ColorU" style="width:90px;"><option value="">Select</option></select></span></td><td><span class="wpcf7-form-control-wrap"><br><select name="SizeU" id="SizeU" style="width:90px;"><option value="">Select</option></select></span></td><td><span class="wpcf7-form-control-wrap"><br><select name="QuantityU" id="QuantityU" style="width:50px;"><option value="1">1</option><option value="2">2</option><option value="3">3</option><option value="4">4</option><option value="5">5</option><option value="6">6</option><option value="7">7</option><option value="8">8</option><option value="9">9</option><option value="10">10</option><option value="11">11</option><option value="12">12</option><option value="13">13</option><option value="14">14</option><option value="15">15</option><option value="16">16</option><option value="17">17</option><option value="18">18</option><option value="19">19</option><option value="20">20</option><option value="21">21</option><option value="22">22</option><option value="23">23</option><option value="24">24</option><option value="25">25</option><option value="26">26</option><option value="27">27</option><option value="28">28</option><option value="29">29</option><option value="30">30</option><option value="31">31</option><option value="32">32</option><option value="33">33</option><option value="34">34</option><option value="35">35</option><option value="36">36</option><option value="37">37</option><option value="38">38</option><option value="39">39</option><option value="40">40</option><option value="41">41</option><option value="42">42</option><option value="43">43</option><option value="44">44</option><option value="45">45</option><option value="46">46</option><option value="47">47</option><option value="48">48</option><option value="49">49</option><option value="50">50</option><option value="51">51</option><option value="52">52</option><option value="53">53</option><option value="54">54</option><option value="55">55</option><option value="56">56</option><option value="57">57</option><option value="58">58</option><option value="59">59</option><option value="60">60</option><option value="61">61</option><option value="62">62</option><option value="63">63</option><option value="64">64</option><option value="65">65</option><option value="66">66</option><option value="67">67</option><option value="68">68</option><option value="69">69</option><option value="70">70</option><option value="71">71</option><option value="72">72</option><option value="73">73</option><option value="74">74</option><option value="75">75</option><option value="76">76</option><option value="77">77</option><option value="78">78</option><option value="79">79</option><option value="80">80</option><option value="81">81</option><option value="82">82</option><option value="83">83</option><option value="84">84</option><option value="85">85</option><option value="86">86</option><option value="87">87</option><option value="88">88</option><option value="89">89</option><option value="90">90</option><option value="91">91</option><option value="92">92</option><option value="93">93</option><option value="94">94</option><option value="95">95</option><option value="96">96</option><option value="97">97</option><option value="98">98</option><option value="99">99</option><option value="100">100</option></select></span></td>
				<td><span class="wpcf7-form-control-wrap">
				<span style="position: relative; bottom: 7px;">
				<div style="width: 100px;overflow: hidden;" id="frontPrintUtext">Not Selected</div>
				<a class="dialog" onclick="SetVar('frontPrintU');" href="#frontPrintU">Select</a>
				<span style="display: none;" id="frontPrintUremove">- <a onclick="remove1('frontPrintU','U');" href="#frontPrintU">Remove</a></span>
				<input type="hidden" name="frontPrintU" id="frontPrintU">
                                    <input value="0" type="hidden" name="frontJumboU" id="frontJumboU">
                                        <input value="0" type="hidden" name="frontUnderbaseU" id="frontUnderbaseU">
                                    
				</span>
				</span></td>
				<td><span class="wpcf7-form-control-wrap">
				<span style="position: relative; bottom: 7px;">
				<div style="width: 100px;overflow: hidden;" id="frontMockupUtext">Not Selected</div>
				<a class="dialog" onclick="SetVar('frontMockupU');" href="#frontMockupU">Select</a>
				<span style="display: none;" id="frontMockupUremove">- <a onclick="remove1('frontMockupU','U');" href="#frontMockupU">Remove</a></span>
				<input type="hidden" name="frontMockupU" id="frontMockupU">
				</span></span></td>
				<td><span class="wpcf7-form-control-wrap" id="BackPrintCellU">
				<span style="position: relative; bottom: 7px;">
				<div style="width: 100px;overflow: hidden;" id="backPrintUtext">Not Selected</div>
				<a class="dialog" onclick="SetVar('backPrintU');" href="#backPrintU">Select</a>
				<span style="display: none;" id="backPrintUremove">- <a onclick="remove1('backPrintU','U');" href="#backPrintU">Remove</a></span>
				<input type="hidden" name="backPrintU" id="backPrintU">
                                <input value="0" type="hidden" name="backJumboU" id="backJumboU">
                                    <input value="0" type="hidden" name="backUnderbaseU" id="backUnderbaseU">
			 	</span></span></td>
				<td><span class="wpcf7-form-control-wrap" id="BackMockupCellU">
				<span style="position: relative; bottom: 7px;">
				<div style="width: 100px;overflow: hidden;" id="backMockupUtext">Not Selected</div>
				<a class="dialog" onclick="SetVar('backMockupU');" href="#backMockupU">Select</a>
				<span style="display: none;" id="backMockupUremove">- <a onclick="remove1('backMockupU','U');" href="#backMockupU">Remove</a></span>
				<input type="hidden" name="backMockupU" id="backMockupU">
                                    
			 </span> </span></td>
				<td><input type="text" style="width:50px;" name="CostU" value="0.00" id="CostU" readonly="">
				<input type="hidden" name="PrintPriceU" value="0" id="PrintPriceU">
                                    
				</td>
				</tr>
				<tr id="holderV" style="display:none;">
				<td><span class="wpcf7-form-control-wrap"><select class="inventory_brand" name="BrandV" id="BrandV" style="width:100px;">
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
<input name="shipping_typeV" id="shipping_typeV" style="display:none;"></span></td><td><span class="wpcf7-form-control-wrap"><input type="hidden" value="1" id="rushedV"><select name="NameV" id="NameV" style="width:100px;"><option value="">Select</option></select></span></td><td><span class="wpcf7-form-control-wrap"><br><select name="ColorV" id="ColorV" style="width:90px;"><option value="">Select</option></select></span></td><td><span class="wpcf7-form-control-wrap"><br><select name="SizeV" id="SizeV" style="width:90px;"><option value="">Select</option></select></span></td><td><span class="wpcf7-form-control-wrap"><br><select name="QuantityV" id="QuantityV" style="width:50px;"><option value="1">1</option><option value="2">2</option><option value="3">3</option><option value="4">4</option><option value="5">5</option><option value="6">6</option><option value="7">7</option><option value="8">8</option><option value="9">9</option><option value="10">10</option><option value="11">11</option><option value="12">12</option><option value="13">13</option><option value="14">14</option><option value="15">15</option><option value="16">16</option><option value="17">17</option><option value="18">18</option><option value="19">19</option><option value="20">20</option><option value="21">21</option><option value="22">22</option><option value="23">23</option><option value="24">24</option><option value="25">25</option><option value="26">26</option><option value="27">27</option><option value="28">28</option><option value="29">29</option><option value="30">30</option><option value="31">31</option><option value="32">32</option><option value="33">33</option><option value="34">34</option><option value="35">35</option><option value="36">36</option><option value="37">37</option><option value="38">38</option><option value="39">39</option><option value="40">40</option><option value="41">41</option><option value="42">42</option><option value="43">43</option><option value="44">44</option><option value="45">45</option><option value="46">46</option><option value="47">47</option><option value="48">48</option><option value="49">49</option><option value="50">50</option><option value="51">51</option><option value="52">52</option><option value="53">53</option><option value="54">54</option><option value="55">55</option><option value="56">56</option><option value="57">57</option><option value="58">58</option><option value="59">59</option><option value="60">60</option><option value="61">61</option><option value="62">62</option><option value="63">63</option><option value="64">64</option><option value="65">65</option><option value="66">66</option><option value="67">67</option><option value="68">68</option><option value="69">69</option><option value="70">70</option><option value="71">71</option><option value="72">72</option><option value="73">73</option><option value="74">74</option><option value="75">75</option><option value="76">76</option><option value="77">77</option><option value="78">78</option><option value="79">79</option><option value="80">80</option><option value="81">81</option><option value="82">82</option><option value="83">83</option><option value="84">84</option><option value="85">85</option><option value="86">86</option><option value="87">87</option><option value="88">88</option><option value="89">89</option><option value="90">90</option><option value="91">91</option><option value="92">92</option><option value="93">93</option><option value="94">94</option><option value="95">95</option><option value="96">96</option><option value="97">97</option><option value="98">98</option><option value="99">99</option><option value="100">100</option></select></span></td>
				<td><span class="wpcf7-form-control-wrap">
				<span style="position: relative; bottom: 7px;">
				<div style="width: 100px;overflow: hidden;" id="frontPrintVtext">Not Selected</div>
				<a class="dialog" onclick="SetVar('frontPrintV');" href="#frontPrintV">Select</a>
				<span style="display: none;" id="frontPrintVremove">- <a onclick="remove1('frontPrintV','V');" href="#frontPrintV">Remove</a></span>
				<input type="hidden" name="frontPrintV" id="frontPrintV">
                                    <input value="0" type="hidden" name="frontJumboV" id="frontJumboV">
                                        <input value="0" type="hidden" name="frontUnderbaseV" id="frontUnderbaseV">
                                    
				</span>
				</span></td>
				<td><span class="wpcf7-form-control-wrap">
				<span style="position: relative; bottom: 7px;">
				<div style="width: 100px;overflow: hidden;" id="frontMockupVtext">Not Selected</div>
				<a class="dialog" onclick="SetVar('frontMockupV');" href="#frontMockupV">Select</a>
				<span style="display: none;" id="frontMockupVremove">- <a onclick="remove1('frontMockupV','V');" href="#frontMockupV">Remove</a></span>
				<input type="hidden" name="frontMockupV" id="frontMockupV">
				</span></span></td>
				<td><span class="wpcf7-form-control-wrap" id="BackPrintCellV">
				<span style="position: relative; bottom: 7px;">
				<div style="width: 100px;overflow: hidden;" id="backPrintVtext">Not Selected</div>
				<a class="dialog" onclick="SetVar('backPrintV');" href="#backPrintV">Select</a>
				<span style="display: none;" id="backPrintVremove">- <a onclick="remove1('backPrintV','V');" href="#backPrintV">Remove</a></span>
				<input type="hidden" name="backPrintV" id="backPrintV">
                                <input value="0" type="hidden" name="backJumboV" id="backJumboV">
                                    <input value="0" type="hidden" name="backUnderbaseV" id="backUnderbaseV">
			 	</span></span></td>
				<td><span class="wpcf7-form-control-wrap" id="BackMockupCellV">
				<span style="position: relative; bottom: 7px;">
				<div style="width: 100px;overflow: hidden;" id="backMockupVtext">Not Selected</div>
				<a class="dialog" onclick="SetVar('backMockupV');" href="#backMockupV">Select</a>
				<span style="display: none;" id="backMockupVremove">- <a onclick="remove1('backMockupV','V');" href="#backMockupV">Remove</a></span>
				<input type="hidden" name="backMockupV" id="backMockupV">
                                    
			 </span> </span></td>
				<td><input type="text" style="width:50px;" name="CostV" value="0.00" id="CostV" readonly="">
				<input type="hidden" name="PrintPriceV" value="0" id="PrintPriceV">
                                    
				</td>
				</tr>
				<tr id="holderW" style="display:none;">
				<td><span class="wpcf7-form-control-wrap"><select class="inventory_brand" name="BrandW" id="BrandW" style="width:100px;">
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
<input name="shipping_typeW" id="shipping_typeW" style="display:none;"></span></td><td><span class="wpcf7-form-control-wrap"><input type="hidden" value="1" id="rushedW"><select name="NameW" id="NameW" style="width:100px;"><option value="">Select</option></select></span></td><td><span class="wpcf7-form-control-wrap"><br><select name="ColorW" id="ColorW" style="width:90px;"><option value="">Select</option></select></span></td><td><span class="wpcf7-form-control-wrap"><br><select name="SizeW" id="SizeW" style="width:90px;"><option value="">Select</option></select></span></td><td><span class="wpcf7-form-control-wrap"><br><select name="QuantityW" id="QuantityW" style="width:50px;"><option value="1">1</option><option value="2">2</option><option value="3">3</option><option value="4">4</option><option value="5">5</option><option value="6">6</option><option value="7">7</option><option value="8">8</option><option value="9">9</option><option value="10">10</option><option value="11">11</option><option value="12">12</option><option value="13">13</option><option value="14">14</option><option value="15">15</option><option value="16">16</option><option value="17">17</option><option value="18">18</option><option value="19">19</option><option value="20">20</option><option value="21">21</option><option value="22">22</option><option value="23">23</option><option value="24">24</option><option value="25">25</option><option value="26">26</option><option value="27">27</option><option value="28">28</option><option value="29">29</option><option value="30">30</option><option value="31">31</option><option value="32">32</option><option value="33">33</option><option value="34">34</option><option value="35">35</option><option value="36">36</option><option value="37">37</option><option value="38">38</option><option value="39">39</option><option value="40">40</option><option value="41">41</option><option value="42">42</option><option value="43">43</option><option value="44">44</option><option value="45">45</option><option value="46">46</option><option value="47">47</option><option value="48">48</option><option value="49">49</option><option value="50">50</option><option value="51">51</option><option value="52">52</option><option value="53">53</option><option value="54">54</option><option value="55">55</option><option value="56">56</option><option value="57">57</option><option value="58">58</option><option value="59">59</option><option value="60">60</option><option value="61">61</option><option value="62">62</option><option value="63">63</option><option value="64">64</option><option value="65">65</option><option value="66">66</option><option value="67">67</option><option value="68">68</option><option value="69">69</option><option value="70">70</option><option value="71">71</option><option value="72">72</option><option value="73">73</option><option value="74">74</option><option value="75">75</option><option value="76">76</option><option value="77">77</option><option value="78">78</option><option value="79">79</option><option value="80">80</option><option value="81">81</option><option value="82">82</option><option value="83">83</option><option value="84">84</option><option value="85">85</option><option value="86">86</option><option value="87">87</option><option value="88">88</option><option value="89">89</option><option value="90">90</option><option value="91">91</option><option value="92">92</option><option value="93">93</option><option value="94">94</option><option value="95">95</option><option value="96">96</option><option value="97">97</option><option value="98">98</option><option value="99">99</option><option value="100">100</option></select></span></td>
				<td><span class="wpcf7-form-control-wrap">
				<span style="position: relative; bottom: 7px;">
				<div style="width: 100px;overflow: hidden;" id="frontPrintWtext">Not Selected</div>
				<a class="dialog" onclick="SetVar('frontPrintW');" href="#frontPrintW">Select</a>
				<span style="display: none;" id="frontPrintWremove">- <a onclick="remove1('frontPrintW','W');" href="#frontPrintW">Remove</a></span>
				<input type="hidden" name="frontPrintW" id="frontPrintW">
                                    <input value="0" type="hidden" name="frontJumboW" id="frontJumboW">
                                        <input value="0" type="hidden" name="frontUnderbaseW" id="frontUnderbaseW">
                                    
				</span>
				</span></td>
				<td><span class="wpcf7-form-control-wrap">
				<span style="position: relative; bottom: 7px;">
				<div style="width: 100px;overflow: hidden;" id="frontMockupWtext">Not Selected</div>
				<a class="dialog" onclick="SetVar('frontMockupW');" href="#frontMockupW">Select</a>
				<span style="display: none;" id="frontMockupWremove">- <a onclick="remove1('frontMockupW','W');" href="#frontMockupW">Remove</a></span>
				<input type="hidden" name="frontMockupW" id="frontMockupW">
				</span></span></td>
				<td><span class="wpcf7-form-control-wrap" id="BackPrintCellW">
				<span style="position: relative; bottom: 7px;">
				<div style="width: 100px;overflow: hidden;" id="backPrintWtext">Not Selected</div>
				<a class="dialog" onclick="SetVar('backPrintW');" href="#backPrintW">Select</a>
				<span style="display: none;" id="backPrintWremove">- <a onclick="remove1('backPrintW','W');" href="#backPrintW">Remove</a></span>
				<input type="hidden" name="backPrintW" id="backPrintW">
                                <input value="0" type="hidden" name="backJumboW" id="backJumboW">
                                    <input value="0" type="hidden" name="backUnderbaseW" id="backUnderbaseW">
			 	</span></span></td>
				<td><span class="wpcf7-form-control-wrap" id="BackMockupCellW">
				<span style="position: relative; bottom: 7px;">
				<div style="width: 100px;overflow: hidden;" id="backMockupWtext">Not Selected</div>
				<a class="dialog" onclick="SetVar('backMockupW');" href="#backMockupW">Select</a>
				<span style="display: none;" id="backMockupWremove">- <a onclick="remove1('backMockupW','W');" href="#backMockupW">Remove</a></span>
				<input type="hidden" name="backMockupW" id="backMockupW">
                                    
			 </span> </span></td>
				<td><input type="text" style="width:50px;" name="CostW" value="0.00" id="CostW" readonly="">
				<input type="hidden" name="PrintPriceW" value="0" id="PrintPriceW">
                                    
				</td>
				</tr>
				<tr id="holderX" style="display:none;">
				<td><span class="wpcf7-form-control-wrap"><select class="inventory_brand" name="BrandX" id="BrandX" style="width:100px;">
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
<input name="shipping_typeX" id="shipping_typeX" style="display:none;"></span></td><td><span class="wpcf7-form-control-wrap"><input type="hidden" value="1" id="rushedX"><select name="NameX" id="NameX" style="width:100px;"><option value="">Select</option></select></span></td><td><span class="wpcf7-form-control-wrap"><br><select name="ColorX" id="ColorX" style="width:90px;"><option value="">Select</option></select></span></td><td><span class="wpcf7-form-control-wrap"><br><select name="SizeX" id="SizeX" style="width:90px;"><option value="">Select</option></select></span></td><td><span class="wpcf7-form-control-wrap"><br><select name="QuantityX" id="QuantityX" style="width:50px;"><option value="1">1</option><option value="2">2</option><option value="3">3</option><option value="4">4</option><option value="5">5</option><option value="6">6</option><option value="7">7</option><option value="8">8</option><option value="9">9</option><option value="10">10</option><option value="11">11</option><option value="12">12</option><option value="13">13</option><option value="14">14</option><option value="15">15</option><option value="16">16</option><option value="17">17</option><option value="18">18</option><option value="19">19</option><option value="20">20</option><option value="21">21</option><option value="22">22</option><option value="23">23</option><option value="24">24</option><option value="25">25</option><option value="26">26</option><option value="27">27</option><option value="28">28</option><option value="29">29</option><option value="30">30</option><option value="31">31</option><option value="32">32</option><option value="33">33</option><option value="34">34</option><option value="35">35</option><option value="36">36</option><option value="37">37</option><option value="38">38</option><option value="39">39</option><option value="40">40</option><option value="41">41</option><option value="42">42</option><option value="43">43</option><option value="44">44</option><option value="45">45</option><option value="46">46</option><option value="47">47</option><option value="48">48</option><option value="49">49</option><option value="50">50</option><option value="51">51</option><option value="52">52</option><option value="53">53</option><option value="54">54</option><option value="55">55</option><option value="56">56</option><option value="57">57</option><option value="58">58</option><option value="59">59</option><option value="60">60</option><option value="61">61</option><option value="62">62</option><option value="63">63</option><option value="64">64</option><option value="65">65</option><option value="66">66</option><option value="67">67</option><option value="68">68</option><option value="69">69</option><option value="70">70</option><option value="71">71</option><option value="72">72</option><option value="73">73</option><option value="74">74</option><option value="75">75</option><option value="76">76</option><option value="77">77</option><option value="78">78</option><option value="79">79</option><option value="80">80</option><option value="81">81</option><option value="82">82</option><option value="83">83</option><option value="84">84</option><option value="85">85</option><option value="86">86</option><option value="87">87</option><option value="88">88</option><option value="89">89</option><option value="90">90</option><option value="91">91</option><option value="92">92</option><option value="93">93</option><option value="94">94</option><option value="95">95</option><option value="96">96</option><option value="97">97</option><option value="98">98</option><option value="99">99</option><option value="100">100</option></select></span></td>
				<td><span class="wpcf7-form-control-wrap">
				<span style="position: relative; bottom: 7px;">
				<div style="width: 100px;overflow: hidden;" id="frontPrintXtext">Not Selected</div>
				<a class="dialog" onclick="SetVar('frontPrintX');" href="#frontPrintX">Select</a>
				<span style="display: none;" id="frontPrintXremove">- <a onclick="remove1('frontPrintX','X');" href="#frontPrintX">Remove</a></span>
				<input type="hidden" name="frontPrintX" id="frontPrintX">
                                    <input value="0" type="hidden" name="frontJumboX" id="frontJumboX">
                                        <input value="0" type="hidden" name="frontUnderbaseX" id="frontUnderbaseX">
                                    
				</span>
				</span></td>
				<td><span class="wpcf7-form-control-wrap">
				<span style="position: relative; bottom: 7px;">
				<div style="width: 100px;overflow: hidden;" id="frontMockupXtext">Not Selected</div>
				<a class="dialog" onclick="SetVar('frontMockupX');" href="#frontMockupX">Select</a>
				<span style="display: none;" id="frontMockupXremove">- <a onclick="remove1('frontMockupX','X');" href="#frontMockupX">Remove</a></span>
				<input type="hidden" name="frontMockupX" id="frontMockupX">
				</span></span></td>
				<td><span class="wpcf7-form-control-wrap" id="BackPrintCellX">
				<span style="position: relative; bottom: 7px;">
				<div style="width: 100px;overflow: hidden;" id="backPrintXtext">Not Selected</div>
				<a class="dialog" onclick="SetVar('backPrintX');" href="#backPrintX">Select</a>
				<span style="display: none;" id="backPrintXremove">- <a onclick="remove1('backPrintX','X');" href="#backPrintX">Remove</a></span>
				<input type="hidden" name="backPrintX" id="backPrintX">
                                <input value="0" type="hidden" name="backJumboX" id="backJumboX">
                                    <input value="0" type="hidden" name="backUnderbaseX" id="backUnderbaseX">
			 	</span></span></td>
				<td><span class="wpcf7-form-control-wrap" id="BackMockupCellX">
				<span style="position: relative; bottom: 7px;">
				<div style="width: 100px;overflow: hidden;" id="backMockupXtext">Not Selected</div>
				<a class="dialog" onclick="SetVar('backMockupX');" href="#backMockupX">Select</a>
				<span style="display: none;" id="backMockupXremove">- <a onclick="remove1('backMockupX','X');" href="#backMockupX">Remove</a></span>
				<input type="hidden" name="backMockupX" id="backMockupX">
                                    
			 </span> </span></td>
				<td><input type="text" style="width:50px;" name="CostX" value="0.00" id="CostX" readonly="">
				<input type="hidden" name="PrintPriceX" value="0" id="PrintPriceX">
                                    
				</td>
				</tr>
				<tr id="holderY" style="display:none;">
				<td><span class="wpcf7-form-control-wrap"><select class="inventory_brand" name="BrandY" id="BrandY" style="width:100px;">
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
<input name="shipping_typeY" id="shipping_typeY" style="display:none;"></span></td><td><span class="wpcf7-form-control-wrap"><input type="hidden" value="1" id="rushedY"><select name="NameY" id="NameY" style="width:100px;"><option value="">Select</option></select></span></td><td><span class="wpcf7-form-control-wrap"><br><select name="ColorY" id="ColorY" style="width:90px;"><option value="">Select</option></select></span></td><td><span class="wpcf7-form-control-wrap"><br><select name="SizeY" id="SizeY" style="width:90px;"><option value="">Select</option></select></span></td><td><span class="wpcf7-form-control-wrap"><br><select name="QuantityY" id="QuantityY" style="width:50px;"><option value="1">1</option><option value="2">2</option><option value="3">3</option><option value="4">4</option><option value="5">5</option><option value="6">6</option><option value="7">7</option><option value="8">8</option><option value="9">9</option><option value="10">10</option><option value="11">11</option><option value="12">12</option><option value="13">13</option><option value="14">14</option><option value="15">15</option><option value="16">16</option><option value="17">17</option><option value="18">18</option><option value="19">19</option><option value="20">20</option><option value="21">21</option><option value="22">22</option><option value="23">23</option><option value="24">24</option><option value="25">25</option><option value="26">26</option><option value="27">27</option><option value="28">28</option><option value="29">29</option><option value="30">30</option><option value="31">31</option><option value="32">32</option><option value="33">33</option><option value="34">34</option><option value="35">35</option><option value="36">36</option><option value="37">37</option><option value="38">38</option><option value="39">39</option><option value="40">40</option><option value="41">41</option><option value="42">42</option><option value="43">43</option><option value="44">44</option><option value="45">45</option><option value="46">46</option><option value="47">47</option><option value="48">48</option><option value="49">49</option><option value="50">50</option><option value="51">51</option><option value="52">52</option><option value="53">53</option><option value="54">54</option><option value="55">55</option><option value="56">56</option><option value="57">57</option><option value="58">58</option><option value="59">59</option><option value="60">60</option><option value="61">61</option><option value="62">62</option><option value="63">63</option><option value="64">64</option><option value="65">65</option><option value="66">66</option><option value="67">67</option><option value="68">68</option><option value="69">69</option><option value="70">70</option><option value="71">71</option><option value="72">72</option><option value="73">73</option><option value="74">74</option><option value="75">75</option><option value="76">76</option><option value="77">77</option><option value="78">78</option><option value="79">79</option><option value="80">80</option><option value="81">81</option><option value="82">82</option><option value="83">83</option><option value="84">84</option><option value="85">85</option><option value="86">86</option><option value="87">87</option><option value="88">88</option><option value="89">89</option><option value="90">90</option><option value="91">91</option><option value="92">92</option><option value="93">93</option><option value="94">94</option><option value="95">95</option><option value="96">96</option><option value="97">97</option><option value="98">98</option><option value="99">99</option><option value="100">100</option></select></span></td>
				<td><span class="wpcf7-form-control-wrap">
				<span style="position: relative; bottom: 7px;">
				<div style="width: 100px;overflow: hidden;" id="frontPrintYtext">Not Selected</div>
				<a class="dialog" onclick="SetVar('frontPrintY');" href="#frontPrintY">Select</a>
				<span style="display: none;" id="frontPrintYremove">- <a onclick="remove1('frontPrintY','Y');" href="#frontPrintY">Remove</a></span>
				<input type="hidden" name="frontPrintY" id="frontPrintY">
                                    <input value="0" type="hidden" name="frontJumboY" id="frontJumboY">
                                        <input value="0" type="hidden" name="frontUnderbaseY" id="frontUnderbaseY">
                                    
				</span>
				</span></td>
				<td><span class="wpcf7-form-control-wrap">
				<span style="position: relative; bottom: 7px;">
				<div style="width: 100px;overflow: hidden;" id="frontMockupYtext">Not Selected</div>
				<a class="dialog" onclick="SetVar('frontMockupY');" href="#frontMockupY">Select</a>
				<span style="display: none;" id="frontMockupYremove">- <a onclick="remove1('frontMockupY','Y');" href="#frontMockupY">Remove</a></span>
				<input type="hidden" name="frontMockupY" id="frontMockupY">
				</span></span></td>
				<td><span class="wpcf7-form-control-wrap" id="BackPrintCellY">
				<span style="position: relative; bottom: 7px;">
				<div style="width: 100px;overflow: hidden;" id="backPrintYtext">Not Selected</div>
				<a class="dialog" onclick="SetVar('backPrintY');" href="#backPrintY">Select</a>
				<span style="display: none;" id="backPrintYremove">- <a onclick="remove1('backPrintY','Y');" href="#backPrintY">Remove</a></span>
				<input type="hidden" name="backPrintY" id="backPrintY">
                                <input value="0" type="hidden" name="backJumboY" id="backJumboY">
                                    <input value="0" type="hidden" name="backUnderbaseY" id="backUnderbaseY">
			 	</span></span></td>
				<td><span class="wpcf7-form-control-wrap" id="BackMockupCellY">
				<span style="position: relative; bottom: 7px;">
				<div style="width: 100px;overflow: hidden;" id="backMockupYtext">Not Selected</div>
				<a class="dialog" onclick="SetVar('backMockupY');" href="#backMockupY">Select</a>
				<span style="display: none;" id="backMockupYremove">- <a onclick="remove1('backMockupY','Y');" href="#backMockupY">Remove</a></span>
				<input type="hidden" name="backMockupY" id="backMockupY">
                                    
			 </span> </span></td>
				<td><input type="text" style="width:50px;" name="CostY" value="0.00" id="CostY" readonly="">
				<input type="hidden" name="PrintPriceY" value="0" id="PrintPriceY">
                                    
				</td>
				</tr>
				<tr id="holderZ" style="display:none;">
				<td><span class="wpcf7-form-control-wrap"><select class="inventory_brand" name="BrandZ" id="BrandZ" style="width:100px;">
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
<input name="shipping_typeZ" id="shipping_typeZ" style="display:none;"></span></td><td><span class="wpcf7-form-control-wrap"><input type="hidden" value="1" id="rushedZ"><select name="NameZ" id="NameZ" style="width:100px;"><option value="">Select</option></select></span></td><td><span class="wpcf7-form-control-wrap"><br><select name="ColorZ" id="ColorZ" style="width:90px;"><option value="">Select</option></select></span></td><td><span class="wpcf7-form-control-wrap"><br><select name="SizeZ" id="SizeZ" style="width:90px;"><option value="">Select</option></select></span></td><td><span class="wpcf7-form-control-wrap"><br><select name="QuantityZ" id="QuantityZ" style="width:50px;"><option value="1">1</option><option value="2">2</option><option value="3">3</option><option value="4">4</option><option value="5">5</option><option value="6">6</option><option value="7">7</option><option value="8">8</option><option value="9">9</option><option value="10">10</option><option value="11">11</option><option value="12">12</option><option value="13">13</option><option value="14">14</option><option value="15">15</option><option value="16">16</option><option value="17">17</option><option value="18">18</option><option value="19">19</option><option value="20">20</option><option value="21">21</option><option value="22">22</option><option value="23">23</option><option value="24">24</option><option value="25">25</option><option value="26">26</option><option value="27">27</option><option value="28">28</option><option value="29">29</option><option value="30">30</option><option value="31">31</option><option value="32">32</option><option value="33">33</option><option value="34">34</option><option value="35">35</option><option value="36">36</option><option value="37">37</option><option value="38">38</option><option value="39">39</option><option value="40">40</option><option value="41">41</option><option value="42">42</option><option value="43">43</option><option value="44">44</option><option value="45">45</option><option value="46">46</option><option value="47">47</option><option value="48">48</option><option value="49">49</option><option value="50">50</option><option value="51">51</option><option value="52">52</option><option value="53">53</option><option value="54">54</option><option value="55">55</option><option value="56">56</option><option value="57">57</option><option value="58">58</option><option value="59">59</option><option value="60">60</option><option value="61">61</option><option value="62">62</option><option value="63">63</option><option value="64">64</option><option value="65">65</option><option value="66">66</option><option value="67">67</option><option value="68">68</option><option value="69">69</option><option value="70">70</option><option value="71">71</option><option value="72">72</option><option value="73">73</option><option value="74">74</option><option value="75">75</option><option value="76">76</option><option value="77">77</option><option value="78">78</option><option value="79">79</option><option value="80">80</option><option value="81">81</option><option value="82">82</option><option value="83">83</option><option value="84">84</option><option value="85">85</option><option value="86">86</option><option value="87">87</option><option value="88">88</option><option value="89">89</option><option value="90">90</option><option value="91">91</option><option value="92">92</option><option value="93">93</option><option value="94">94</option><option value="95">95</option><option value="96">96</option><option value="97">97</option><option value="98">98</option><option value="99">99</option><option value="100">100</option></select></span></td>
				<td><span class="wpcf7-form-control-wrap">
				<span style="position: relative; bottom: 7px;">
				<div style="width: 100px;overflow: hidden;" id="frontPrintZtext">Not Selected</div>
				<a class="dialog" onclick="SetVar('frontPrintZ');" href="#frontPrintZ">Select</a>
				<span style="display: none;" id="frontPrintZremove">- <a onclick="remove1('frontPrintZ','Z');" href="#frontPrintZ">Remove</a></span>
				<input type="hidden" name="frontPrintZ" id="frontPrintZ">
                                    <input value="0" type="hidden" name="frontJumboZ" id="frontJumboZ">
                                        <input value="0" type="hidden" name="frontUnderbaseZ" id="frontUnderbaseZ">
                                    
				</span>
				</span></td>
				<td><span class="wpcf7-form-control-wrap">
				<span style="position: relative; bottom: 7px;">
				<div style="width: 100px;overflow: hidden;" id="frontMockupZtext">Not Selected</div>
				<a class="dialog" onclick="SetVar('frontMockupZ');" href="#frontMockupZ">Select</a>
				<span style="display: none;" id="frontMockupZremove">- <a onclick="remove1('frontMockupZ','Z');" href="#frontMockupZ">Remove</a></span>
				<input type="hidden" name="frontMockupZ" id="frontMockupZ">
				</span></span></td>
				<td><span class="wpcf7-form-control-wrap" id="BackPrintCellZ">
				<span style="position: relative; bottom: 7px;">
				<div style="width: 100px;overflow: hidden;" id="backPrintZtext">Not Selected</div>
				<a class="dialog" onclick="SetVar('backPrintZ');" href="#backPrintZ">Select</a>
				<span style="display: none;" id="backPrintZremove">- <a onclick="remove1('backPrintZ','Z');" href="#backPrintZ">Remove</a></span>
				<input type="hidden" name="backPrintZ" id="backPrintZ">
                                <input value="0" type="hidden" name="backJumboZ" id="backJumboZ">
                                    <input value="0" type="hidden" name="backUnderbaseZ" id="backUnderbaseZ">
			 	</span></span></td>
				<td><span class="wpcf7-form-control-wrap" id="BackMockupCellZ">
				<span style="position: relative; bottom: 7px;">
				<div style="width: 100px;overflow: hidden;" id="backMockupZtext">Not Selected</div>
				<a class="dialog" onclick="SetVar('backMockupZ');" href="#backMockupZ">Select</a>
				<span style="display: none;" id="backMockupZremove">- <a onclick="remove1('backMockupZ','Z');" href="#backMockupZ">Remove</a></span>
				<input type="hidden" name="backMockupZ" id="backMockupZ">
                                    
			 </span> </span></td>
				<td><input type="text" style="width:50px;" name="CostZ" value="0.00" id="CostZ" readonly="">
				<input type="hidden" name="PrintPriceZ" value="0" id="PrintPriceZ">
                                    
				</td>
				</tr>	
			</tbody></table>
			<br>
			<div style="width: 175px;float: right;text-align: right;"><a href="#details" id="addanother">Add Another Item </a>
			</div>
			<br style="clear: both;"><br><p><span class="wpcf7-form-control-wrap ShippingMethod"><span>Shipping Method (required)</span>&nbsp;&nbsp;<select class="inventory_size" name="ShippingMethod" id="ShippingMethod" style="width:auto;display: inline">
<option value="1">Standard Shipping (United States)</option>
</select>&nbsp;&nbsp;Shipping Cost: $<span id="shippingcost">0.00</span></span></p><p></p><p>Neck Brand Label Removal? ($0.50 each) <span class="wpcf7-form-control-wrap"><br>
                        <span style="font-style:italic;font-weight:normal">Only the brand label will be removed. The other tag is legally required unless it is replaced with your own label that meets the legal guidelines for apparel labeling. Read more about our <a target="_blank" href="/your-brand/">label service</a></span>
			<select class="wpcf7-form-control  wpcf7-select" name="tagremoval" id="tagremoval">
			<option value="No">No</option>
			<option value="Yes">Yes</option>
			</select>
			</span></p><p>Individual Bagging($1.00 each) <span class="wpcf7-form-control-wrap"><br>
                        <span style="font-style:italic;font-weight:normal">Each shirt will be folded in its own clear polybag with a size sticker on the outside. </span>
			<select class="wpcf7-form-control  wpcf7-select" name="individualbagging" id="individualbagging">
			<option value="No">No</option>
			<option value="Yes">Yes</option>
			</select>
			</span>
			</p>
                            <p>Rush Order ($2.00 each) <span class="wpcf7-form-control-wrap rushorder"><br>
                        <span style="font-style:italic;font-weight:normal">Rush processing is typically 2-3 business days instead of 4-5 business days. Daily cutoff is 12:30ET. </span>
			<select class="wpcf7-form-control  wpcf7-select" name="rushorder" id="rushorder">
			<option value="No">No</option>
			<option value="Yes">Yes</option>
			</select>
			</span>
			</p>
			<p style="display:none;">Special Instructions (optional)<br>
			<span class="wpcf7-form-control-wrap SpecialInstructions"><textarea rows="10" cols="40" class="wpcf7-form-control  wpcf7-textarea" name="SpecialInstructions"></textarea></span></p>
			<p><input type="hidden" class="wpcf7-hidden" value="" name="downloadAllURL"></p>
                        <p><input type="submit" id="btnSend2" value="Submit Order" style="margin-top: 10px;padding:15px;"></p>
			<div class="wpcf7-response-output wpcf7-validation-errors" id="validate_err" style="display:none;">Validation errors occurred. Please confirm the fields and submit it again.</div>
			<input type="hidden" name="t_removal" id="t_removal" value="0.50"><input type="hidden" name="t_application" id="t_application" value="0.50"><input type="hidden" name="attach_hang_tag" id="attach_hang_tag" value="0.50"><input type="hidden" name="a_material" id="a_material" value="0.25"><input type="hidden" name="i_bagging" id="i_bagging" value="1.00"><input type="hidden" name="c_packaging" id="c_packaging" value="0.25"><input type="hidden" name="a_rush" id="a_rush" value="2.00"></form></div>
	
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.9.0/jquery.min.js"></script>
    <link href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.9.0/themes/base/jquery-ui.css" rel="stylesheet">
    <script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.1/jquery-ui.min.js"></script>
    <script>
    
 if (!Array.prototype.indexOf)
{
  Array.prototype.indexOf = function(elt /*, from*/)
  {
    var len = this.length >>> 0;

    var from = Number(arguments[1]) || 0;
    from = (from < 0)
         ? Math.ceil(from)
         : Math.floor(from);
    if (from < 0)
      from += len;

    for (; from < len; from++)
    {
      if (from in this &&
          this[from] === elt)
        return from;
    }
    return -1;
  };
}   
function autoResize(id){
    var newheight;
    var newwidth;

    if(document.getElementById){
        newheight=document.getElementById(id).contentWindow.document.body.scrollHeight;
        newwidth=document.getElementById(id).contentWindow.document.body.scrollWidth;
    }

    document.getElementById(id).height= (newheight) + "px";
}

	function abc(imageid,imagename,is_jumbo,nounderbase){
 // console.log(is_jumbo);
 // console.log(imageselection);
 
  jQuery("#"+imageselection+"text").html(imagename);
  jQuery("#dialog-form").dialog( "close" );
  jQuery("#"+imageselection).val(imageid);
  
  var letter = imageselection.substr(-1);
 var  print = imageselection.substr(0,imageselection.length-1);
  jQuery("#"+imageselection+"remove").show();
  //console.log(print);
  if(print=="frontPrint" && is_jumbo==1){
  jQuery("#frontJumbo"+letter).val(1);
  }
  else if(print=="frontPrint" && is_jumbo==0){
  jQuery("#frontJumbo"+letter).val(0);
  }
   if(print=="frontPrint" && nounderbase==1){
  jQuery("#frontUnderbase"+letter).val(1);
  }
  else if( print=="frontPrint" && nounderbase==0){
   jQuery("#frontUnderbase"+letter).val(0);
  }
  if(print=="backPrint" && is_jumbo==1){
    jQuery("#backJumbo"+letter).val(1);
  }
  else if(print=="backPrint" && is_jumbo==0){
  jQuery("#backJumbo"+letter).val(0);
  }
  if(print=="backPrint" && nounderbase==1){
  jQuery("#backUnderbase"+letter).val(1);
  }
  else if(print=="backPrint" && nounderbase==0){
  jQuery("#backUnderbase"+letter).val(0);
  }
  cost_caluculation(letter,print);

}

         function remove1(value,val){
  jQuery("#"+value+"text").html("Not Selected");
  jQuery("#"+value+"remove").hide();
  jQuery("#"+value).val("");
  if(value == "frontPrint"+val){
   jQuery("#frontJumbo"+val).val(0);
   jQuery("#frontUnderbase"+val).val(0);
  cost_caluculation(val,"frontPrint");
  
  }else if(value == "backPrint"+val){
  jQuery("#backJumbo"+val).val(0);
  jQuery("#backUnderbase"+val).val(0);
    cost_caluculation(val,"backPrint");
    
  }
}

function SetVar(variable){
imageselection = variable;
}

jQuery(".dialog").click(function (){
jQuery( "#dialog-form" ).dialog( "open" );
});

var winH = jQuery(window).height() - 180;
document.getElementById("iframe1").height= winH+"px";

jQuery( "#dialog-form" ).dialog({
    height: winH,
    width: 1030,
    modal: true,
     autoOpen: false,
    close: function() {
      },
      open: function (event, ui) {
    jQuery("#dialog-form").css("overflow", "hidden"); 
    jQuery("#dialog-form").css("padding-bottom", "30px"); 
  }
    });
    
jQuery("#tagapplication").change(function(){
var elem = jQuery("#tagapplication option:selected");
if (elem.val()=="No"){
jQuery("#location_hpt").hide();
}
if (elem.val()=="Yes"){
jQuery("#location_hpt").show();
}
});

jQuery("#attachhangtag").change(function(){
var elem = jQuery("#attachhangtag option:selected");
if (elem.val()=="No"){
jQuery("#location_aht").hide();
}
if (elem.val()=="Yes"){
jQuery("#location_aht").show();
}
});

    </script>

   <!--  END This is what's rendered from display_functions.php -->
</div></div></div>

    <?php get_footer(); ?>