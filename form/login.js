const queryString = window.location.search
const urlParams = new URLSearchParams(queryString);
var pagename = location.pathname.split("/").slice(-1)
document.querySelector('#validate-form').addEventListener('submit', e=>{
  e.preventDefault();

  let formData = new FormData();

  if((document.getElementById('g-recaptcha-response').value).length == 0){
    login_status('Recaptcha required');
    return;
  }
  formData.append('username', document.getElementById('username').value);
  formData.append('password', document.getElementById('password').value);
  formData.append('g-recaptcha-response', document.getElementById('g-recaptcha-response').value);
  fetchform(formData);
})

async function fetchform(formData) {
  const response = await fetch('form/fetch.php',{
    method: 'POST',
    body: formData
  });

  json = await response.json();

  if(!json.success){
    login_status(json.message);
    grecaptcha.reset();
  }else{
    location.reload();
  }

}

function login_status(data) {
  document.getElementById('login_status').innerHTML = `
    <div class="alert alert-dismissable alert-danger">
      ${data}
      <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
    </div>
  `;
}