/*  jquery function to process ajax request     */
(function ($) {
  $(document).ready(function () {
    $(document).on("change", "", function (e) {
      e.preventDefault();
      var cf = $("#event-type").find("option:selected").val();
      var cfd = $("#event-month").find("option:selected").val();
      $.ajax({
        url: my_ajax_url.ajax_url,
        data: { action: "filter", customfield: cf, customfieldone: cfd },
        type: "post",
        success: function (result) {
          $("[data-js-filter=target]").html(result);
        },
        error: function (result) {
          console.warn(result);
        },
      });
    });
  });
})(jQuery);
