<?php echo $header; ?><?php if ($version == 2) { echo $column_left; } ?>
<div id="content">
<?php if ($version == 1) : ?>
  <div class="breadcrumb">
    <?php foreach ($breadcrumbs as $breadcrumb) { ?>
    <?php echo $breadcrumb['separator']; ?><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
    <?php } ?>
  </div>
  <div class="box">
    <div class="heading">
      <h1><img src="view/image/module.png" alt="" /> <?php echo $heading_title; ?></h1>
    </div>
<?php elseif ($version == 2) : ?>
    <div class="box">
      <div class="heading">
        <h1><?php echo $heading_title; ?></h1>
        <ul class="breadcrumb">
          <?php foreach ($breadcrumbs as $breadcrumb) { ?>
          <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
          <?php } ?>
        </ul>
      </div>
<?php endif; ?>
    <div class="content">
      <div class="cart2cart">
        <div id = "messages"></div>
        <div class="cart2cart_topdesc">
          <?php echo $cart2cart_logo; ?>
          <img height="81" width="300" alt="Automated shopping cart migration service" title="Automated shopping cart migration service" src="view/image/cart2cart/logo.png">
          </a>
          <div class="cart2cart_text">Cart2Cart is an automated shopping cart migration service. It gives a unique opportunity to move data from one shopping cart to another.
            <?php echo $cartName; ?> Migration Module by Cart2Cart helps to move products, customers, orders and other related information TO   <?php echo $cartName; ?> store.
          </div>
        </div>
        <?php if($loginStatus != 'No'): ?>
        <div class="c2c_topic">
          <div class="c2c_logged">
            <input type="hidden" id="isLogged" value="<?php echo $loginStatus; ?>">
            <p>You are logged in as <strong id ="loggedEmail"><?php echo $Cart2CartLoginEmail; ?></strong> <a class="logout">Logoff</a></p>
          </div>
        </div>
        <?php endif; ?>

        <div class="tabs_content">
          <?php if ($loginStatus == 'No'): ?>
      <span class="nav_bg">
      <ul class="nav_tabs">
        <li><a href="#c2c_login" class="selected"><i class="fa fa-sign-out"></i>Login</a></li>
        <li><a href="#c2c_register"><i class="fa fa-pencil-square-o"></i>Register</a></li>
      </ul>
      </span>
          <?php elseif($loginStatus != 'No'): ?>
      <span class="nav_bg">
        <ul class="nav_tabs">
          <li><a id="setup_source_link" href="#setup_source" class="selected"><i class="fa fa-upload"></i><?php echo $sourceCartName ?> Bridge Setup</a></li>
          <li><a id="setup_target_link" href="#setup_target"><i class="fa fa-upload"></i><?php echo $cartName; ?> Bridge Setup</a></li>
          <li><a id="start_migration_link" href="#start_migration"><i class="fa fa-arrow-circle-o-right"></i>Start Migration</a></li>
        </ul>
      </span>
          <?php endif; ?>

          <?php if($loginStatus == 'No'): ?>
          <input type="hidden" id="isLogged" value="<?php echo $loginStatus; ?>">
          <p>To start your migration you will have to register Cart2Cart account or login with existing one. Please, choose appropriate option and proceed to the next step.</p>
          <div id="c2c_login_content">
            Enter your email<br/>
            <input type="text" size="30" id = "loginCart2cartAccount" value="<?php echo $Cart2CartLoginEmail; ?>"><div class="c2cReqired" id="cart2cartAccount"></div><br/>
            Enter your password<br/>
            <input type="password" size="30" id = "loginCart2cartPass"><div class="c2cReqired" id="cart2cartPass"></div><br/>
            <button class="submit_button" id='submitLoginForm'>Login</button>
          </div>
          <div id="c2c_register_content">
            Enter your full name<br/>
            <input type="text" size="30" id = "registerCart2cartName"><div class="c2cReqired" id="registerAccountError"></div><br/>
            Enter your email<br/>
            <input type="text" size="30" id = "registerCart2cartAccount"><div class="c2cReqired" id="registerEmailError"></div><br/>
            Enter your password<br/>
            <input type="password" size="30" id = "registerCart2cartPass"><div class="c2cReqired" id="registerPassError"></div><br/>
            <input type="hidden" id = "registerRefererText" value="<?php echo $referer_text; ?>">
            <button class="submit_button" id='submitRegisterForm'>Register</button>
          </div>
          <?php elseif($loginStatus != 'No'): ?>
          <div id ="setup_source_content">
            <input type="hidden" id="cart2CartLoginKey" value="<?php echo $Cart2CartLoginKey; ?>">
            <input type="hidden" id="cart2CartLoginEmail" value="<?php echo $Cart2CartLoginEmail; ?>">
            <div class="descSourceCart">
              <div class="logoBlur"><img src="<?php echo $sourceCartLogo; ?>"/></div><span>In order to make shopping cart migration possible enter your <?php echo $sourceCartName; ?> FTP details. It will allow to upload the Connection Bridge* to your store.</span>
            </div>
            <div id="sourceConnection">
              Enter FTP host<br/>
              <input type="text" size="30" id="Cart2cartRemoteHost" value="<?php echo $Cart2CartRemoteHost; ?>" ><div class="c2cReqired" id="hostError"></div><br/>
              Enter FTP username<br/>
              <input type="text" size="30" id="Cart2cartRemoteUsername" value="<?php echo $Cart2CartRemoteUsername; ?>" ><div class="c2cReqired" id="hostUser"></div><br/>
              Enter FTP password<br/>
              <input type="password" size="30" id="Cart2cartRemotePassword" value="" ><br/>
              Enter remote directory<br/>
              <input type="text" size="30" id="Cart2cartRemoteDirectory" value="<?php echo $Cart2CartRemoteDirectory; ?>" ><br/>
              <button id = 'submitCart2cartRemoteForm'>Install Connection Bridge</button>
            </div>
            <?php echo $banner; ?>
            <div class="clear"></div>
            <p class="next_step_align"><a href="#setup_target" class="next_step">Proceed to the Next Step</a></p>
            <p>* Connection Bridge files are used to retrieve information from your Source Store and move it to <?php echo $cartName; ?>. These special access gateways are secured by unique tokens for reliable data processing. If you shopping cart is not open source and you don’t have an access to your FTP please start your migration directly at Cart2Cart website. </p>
          </div>
          <div id="setup_target_content">
            <div class="textAlign">
              <img src="view/image/cart2cart/opencart_logo.png"/>
              <span>The Connection Bridge for your <?php echo $cartName; ?> store will be uploaded automatically. Simply click the button Install <?php echo $cartName; ?> Connection Bridge. You will be able to uninstall it right after the data transfer is completed. However, make sure to not remove it until the full migration is over. Otherwise it will cause data transfer issues.</span>
            </div>
            <?php echo $banner; ?>
            <div class="clear"></div>
            <div id = "bridgeConnection"><input type="hidden" id="showButton" value="<?php echo $showButton; ?>">
              <button id='Cart2cartConnectionUninstall'>Uninstall Connection Bridge</button>
              <button id='Cart2cartConnectionInstall'>Install Connection Bridge </button>
            </div>
            <p class="next_step_align"><a href="#start_migration" class="next_step">Proceed to the Next Step</a></p>
          </div>
          <div id="start_migration_content">
            <div class="textAlign">
              <div class="pairCarts">
                <div class="logoBlur"><img src="<?php echo $sourceCartLogo; ?>"/></div>
                <i class="fa fa-caret-right" style="float:left;font-size:32px;"></i>
                <div class="logoBlur"><img src="http://www.shopping-cart-migration.com/images/stories/<?php echo strtolower($cartName); ?>.gif"/></div>
              </div>
              <div class="clear"></div>
              <span>Now you are ready to start your migration from <?php echo $sourceCartName; ?> to <?php echo $cartName; ?>. You will be redirected to Cart2Cart site. Please make sure to specify your <?php echo $sourceCartName; ?> and <?php echo $cartName; ?> stores URL. After that you will be ready to complete full migration</span>
            </div>
            <?php echo $banner; ?>
            <div class="clear"></div>
            <p class="next_step_align"><button type="button" id="startMigration" onclick="javascript: window.open('https://app.shopping-cart-migration.com/from-<?php echo strtolower($sourceCartNameLink); ?>-to-<?php echo strtolower($cartName); ?>/migrations/wizard2/new','_blank');
     return false;"><span>Start Migration</span></button></p>
          </div>
          <?php endif; ?>
        </div>
        <span class="cart2cart_support"><span class="cart2cart_support_icon">Need a hand with your migration? Don’t hesitate and contact our <a href="http://support.magneticone.com/index.php?/Tickets/Submit/RenderForm/5" target="_blank">Support Team</a>. It's our pleasure to help you.</span></span>
      </div>


    </div>
  </div>
</div>
<?php echo $footer; ?>