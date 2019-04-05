<div class="modal fade" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true" id="payment-modal">
  <div class="modal-dialog modal-md">
    <?php if (!empty($error)) { ?>
    <div class="warning"><?php echo $error; ?></div>
    <?php } ?>
    <div class="modal-content">
      <style scoped>
        /* Journal Only styles */
        .modal-body {
          padding: 0;
        }

        .panel-heading {
          background: black;
        }

        .panel-heading h1,
        .panel-heading h2,
        .panel-heading h3 {
          text-transform: uppercase;
        }

        .panel-body {
          padding-top: 25px; /* Offset extra width of payment form */
        }

        .panel-heading .panel-title,
        .panel-heading button.close {
          display: inline-block;
        }

        .panel-heading button.close {
          color: white !important;
          position: relative;
          top: -4px;
          opacity: 0.777;
        }

        .buttons.hidden {
          background: none;
          height: 1px;
          visibility: hidden;
          padding: 0 !important;
          margin: 0 !important;
        }
      </style>
      <div class="modal-body">
        <div class="panel panel-default">
          <div class="panel-heading">
            <h3 class="panel-title"><i class="fa fa-credit-card"></i> Credit Card Payment</h3>
            <button style="float: right;" aria-hidden="true" data-dismiss="modal" class="close" type="button">x</button>
          </div>
          <div class="panel-body">
            <iframe id="payment_iframe" name="payment_iframe" height="385px" width="100%" frameBorder="0"></iframe>

            <form action="<?php echo $action; ?>" method="post" id="checkout-form" target="payment_iframe">
              <?php foreach ($fields as $key => $value) { ?>
              <input type="hidden" name="<?php echo $key; ?>" value="<?php echo $value; ?>" />
              <?php } ?>
            </form>

            <!-- Might be necessary for other templates -->
            <div class="buttons" style="visibility: hidden; height: 1px; overflow: hidden">
              <div class="pull-right">
                <input type="button" value="<?php echo $button_confirm; ?>" id="button-confirm" class="btn btn-primary" />
              </div>
            </div>
            <script type="text/javascript">
              var w = $('body').children('.payment-modal');
              if (!(w.length > 0)) {
                // Detach the modal and append it to the body tag so the screen overlay is sized correctly
                // Because there's now more than one of the same ID in the doc, we have to fry the other one
                // Anyway, shouldn't f***ing detach do that for me? Noooooo...
                w = $('#payment-modal').detach().appendTo('body');
                $('#payment-confirm-button #payment-modal').remove();

                w.find('.buttons').addClass('hidden'); // TODO: Show if not Journal2
              }

              $('.confirm-button').each(function () {
                $(this).attr('data-toggle', 'modal');
                $(this).attr('data-target', '#payment-modal');
              });

              $('.confirm-button').on('click', function (e) {
                triggerLoadingOff();
              });

              // Necessary for other templates - Journal2 triggers the click programmatically
              // TODO: Make a default template for other themes so I don't have an extra confirm button sitting around
              $('#button-confirm').on('click', function() {
                $('#checkout-form').submit();
              });
            </script>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>