var myInput1 = document.getElementById("password");
var myInput2 = document.getElementById("confirm-password");
var letter = document.getElementById("letter");
var capital = document.getElementById("capital");
var number = document.getElementById("number");
var minlength = document.getElementById("minlength");
var maxlength = document.getElementById("maxlength");
var specialchar = document.getElementById("specialchar");
var samepass = document.getElementById("samepass");
var button = document.getElementById("submit-button");

// When the user starts to type something inside the password field
function validate_form_passwords() {
  button.disabled='true';
  // Validate lowercase letters
  var lowerCaseLetters = /[a-z]/g;
  if(myInput1.value.match(lowerCaseLetters)) {
    letter.classList.remove("invalid");
    letter.classList.add("valid");
  } else {
    letter.classList.remove("valid");
    letter.classList.add("invalid");
  }

  // Validate capital letters
  var upperCaseLetters = /[A-Z]/g;
  if(myInput1.value.match(upperCaseLetters)) {
    capital.classList.remove("invalid");
    capital.classList.add("valid");
  } else {
    capital.classList.remove("valid");
    capital.classList.add("invalid");
  }

  // Validate numbers
  var numbers = /[0-9]/g;
  if(myInput1.value.match(numbers)) {
    number.classList.remove("invalid");
    number.classList.add("valid");
  } else {
    number.classList.remove("valid");
    number.classList.add("invalid");
  }

  // Validate min length
  if(myInput1.value.length >= 12) {
    minlength.classList.remove("invalid");
    minlength.classList.add("valid");
  } else {
    minlength.classList.remove("valid");
    minlength.classList.add("invalid");
  }

  // Validate max length
  if(myInput1.value.length <= 24) {
    maxlength.classList.remove("invalid");
    maxlength.classList.add("valid");
  } else {
    maxlength.classList.remove("valid");
    maxlength.classList.add("invalid");
  }

  // Validate special char
  var format = /[!-\/:-@[-`{-~]/;
  if(myInput1.value.match(format)) {
    specialchar.classList.remove("invalid");
    specialchar.classList.add("valid");
  } else {
    specialchar.classList.remove("valid");
    specialchar.classList.add("invalid");
  }

  // Validate if password are the same in both fields
  if (myInput1.value === myInput2.value) {
    samepass.classList.remove("invalid");
    samepass.classList.add("valid");
  } else {
    samepass.classList.remove("valid");
    samepass.classList.add("invalid");
  }  

  //RELEASE BUTTON IF ALL IS OK
  if (myInput1.value.match(lowerCaseLetters) &&
	myInput1.value.match(upperCaseLetters) &&
	myInput1.value.match(numbers) &&
	myInput1.value.length >= 12 &&
	myInput1.value.length <= 24 &&
	myInput1.value.match(format) &&
	myInput1.value == myInput2.value
  ) {
    button.disabled = false;
  }
}

myInput1.onkeyup = validate_form_passwords;
myInput2.onkeyup = validate_form_passwords;
