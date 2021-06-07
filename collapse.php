<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8">
		<title>UETCheckList</title>
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:400,400i,500,500i,700,700i,900,900i">
        <link rel="stylesheet" href="styles/reset.css">
        <link rel="stylesheet" href="-styles/debug.css">
        <link rel="stylesheet" href="styles/indexx.css">
        <link rel="stylesheet" href="styles/table.css">

        <link rel="stylesheet" href="collapse.css">

	</head>
<body>

<h2>Animated Collapsibles</h2>

<p>Collapsible Set:</p>
<button class="collapsible">Open Section 1</button>


<div class="content">
  <p>a</p>
</div>

<form action="http://localhost/test/UETchecklist/index.php">
  <input class="btn" type="submit" value="Trang chủ">
</form>

<script>
var coll = document.getElementsByClassName("collapsible");
var i;

for (i = 0; i < coll.length; i++) {
  coll[i].addEventListener("click", function() {
    this.classList.toggle("active");
    var content = this.nextElementSibling;
    if (content.style.maxHeight){
      content.style.maxHeight = null;
    } else {
      content.style.maxHeight = content.scrollHeight + "px";
    } 
  });
}
</script>

</body>
</html>