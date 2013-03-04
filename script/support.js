function formatDate(source) {
    var output = '';
    var value = seperateDate(source);

    if (value.days > 0) {
        output += value.days + ' days, ';
    }
    if (value.hours > 0) {
        output += value.hours + ' hours, ';
    }
    if (value.minutes > 0) {
        output += value.minutes + ' minutes, ';
    }
    if (value.seconds > 0) {
        output += value.seconds + ' seconds ';
    }

    if (output.substring(output.length - 2) == ', ') {
        output = output.substring(0, output.length - 2);
    }
    return output;
}

function seperateDate(source) {
    var d = new Date();
    var n = d.getTime();
    n = Math.floor(n / 1000);
    n = n - source;
    var value = new Object();
    value.seconds = 0;
    value.minutes = 0;
    value.hours = 0;
    value.days = 0;
    value.seconds = Math.floor(n % 60);
    n = n / 60;
    value.minutes = Math.floor(n % 60);
    n = n / 60;
    value.hours = Math.floor(n % 24);
    n = n / 24;
    value.days = Math.floor(n);

    return value;
}