jQuery(document).ajaxComplete(function(event, xhr, settings){
  // Get the data string and find the action
  var match = settings.data.match("action=(.*)&event");

  // If the action is adding a booking event, redirect to review booking page
  if (match[1] == 'booking_add')
    window.location.href = '/review-registrations';
});