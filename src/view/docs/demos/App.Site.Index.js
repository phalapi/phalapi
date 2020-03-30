$.ajax({
      url: "{url}",
      data: {s: "{s}", username: "PhalApi"},
      dataType: 'json',
      success: function (response, status, xhr) {
          console.log(response);
      }
});
