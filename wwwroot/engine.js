window.addEventListener("load", fetchAsync);

function fetchAsync() {
  
  document.getElementById("blog").addEventListener("click",barClicked);
  document.getElementById("about").addEventListener("click",barClicked);
  let url = window.location.pathname;
  let hash = url.split('/')[2];

  if(url == "/"){
    fetch("/blog.php").then(function(response){
      return response.json();
    }).then(function(x){
      let blog = document.getElementById('content');
      x.map((o)=> {
        let item = document.createElement('div');
        let post = document.createElement('a');
        post.setAttribute("hash",o.hash);
        post.addEventListener("click",getpost);
        post.textContent = o.name;
        var date = document.createTextNode(o.date + " ");
        item.appendChild(date);
        item.appendChild(post);
        blog.appendChild(item);
      });
      document.body.appendChild(blog);
    });
  }
  else if(hash != null){
    loadpost(hash);
  }
  else{
    loadpost('about');
  }
}

getpost = (event) => {
  let hash = event.target.getAttribute('hash');
  loadpost(hash);
  window.history.replaceState( {} , 'foo', "/post/"+hash );
};

barClicked = (event) => {
  var content = document.getElementById("content");
  while (content.firstChild) {
    content.removeChild(content.firstChild);
  }
  let url = (event.target.getAttribute("id") == 'blog') ? '/' : '/about';
  window.history.replaceState( {} , 'foo', url );
  fetchAsync();
};

function loadpost(hash){
  let data = {post:hash};
  fetch("/blog.php",{
    method:"POST",
    headers:{
      'Content-Type': 'application/json'
    },
    body: JSON.stringify(data),
  }).then(function(response){
    return response.text();
  }).then(function (data){
    document.getElementById("content").innerHTML = data;
  });
}