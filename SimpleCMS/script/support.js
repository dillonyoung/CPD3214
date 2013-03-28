/**
 * Description: Contains the support features for the application
 * Filename...: support.js
 * Author.....: Dillon Young (C0005790)
 * 
 */

 /**
  * Displays a message at the top of the screen
  *
  * @param message The message to be displayed
  * @param state The state type for the message
  *
  */
function displayMessage(message, state) {

    // Sets the text of the message
    $('#status_message').html(message);

    // Set the style of the message based on the state
    if (state == 1) {
        $('#status_message').attr('class', 'status_message_valid');
    } else if (state == 2) {
        $('#status_message').attr('class', 'status_message_invalid');
    } else if (state == 3) {
        $('#status_message').attr('class', 'status_message_warn');
    }

    // Set the display settings for the message
    $('#status_message').fadeIn(500).delay(3000).fadeOut(500);
}

/**
 * Formats a date into days, hours, minutes and seconds
 *
 * @param source The source date value
 * @return Returns the formatted date
 *
 */
function formatDate(source) {

    // Set the initial values
    var output = '';

    // Seperate the date
    var value = seperateDate(source);

    // Check to see if there is at least 1 day and update the the output
    if (value.days > 0) {
        output += value.days + ' days, ';
    }

    // Check to see if there is at least 1 hour and update the output
    if (value.hours > 0) {
        output += value.hours + ' hours, ';
    }

    // Check to see if there is at least 1 minute and update the output
    if (value.minutes > 0) {
        output += value.minutes + ' minutes, ';
    }

    // Check to see if there is at least 1 second and update the output
    if (value.seconds > 0) {
        output += value.seconds + ' seconds ';
    }

    // Check to see if the output ends in a comma and remove it
    if (output.substring(output.length - 2) == ', ') {
        output = output.substring(0, output.length - 2);
    }

    // Return the formatted date
    return output;
}

/**
 * Seperates a date into an array
 *
 * @param source The source date value
 * @return Returns an object of the date seperated
 *
 */
function seperateDate(source) {

    // Get the current date and time
    var d = new Date();
    var n = d.getTime();

    // Remove the milliseconds
    n = Math.floor(n / 1000);

    // Get the difference in the current date and the source date
    n = n - source;

    // Create and initialize the object
    var value = new Object();
    value.seconds = 0;
    value.minutes = 0;
    value.hours = 0;
    value.days = 0;

    // Get the number of seconds
    value.seconds = Math.floor(n % 60);
    n = n / 60;

    // Get the number of minutes
    value.minutes = Math.floor(n % 60);
    n = n / 60;

    // Get the number of hours
    value.hours = Math.floor(n % 24);
    n = n / 24;

    // Get the number of days
    value.days = Math.floor(n);

    // Return the object
    return value;
}

/**
 * Redirect the user back to the previous page
 *
 */
function redirectUser() {
    window.location.href = window.document.referrer;
}