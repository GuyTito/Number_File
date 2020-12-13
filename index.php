<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>sort_web_dev</title>
    <style>
        .draggable {margin: 0.5rem;padding: 0.5rem;background-color: #F5F5F5;border: 1px solid black;cursor: move;width: 60%;}
        .dragging {opacity: 0.01;}
    </style>
</head>
<body>
<h1>Number Your Files</h1>
<form method="POST">
    <label for="path">Enter folder path<br>
        Eg: <span><em>file:///C:/Users/JohnDoe/Videos</em></span></label><br>
    <input style="width: 500px" name="path" id="path" type="text"><br><br>
    <input type="submit" name="showFiles" value="Show files"><br>
</form>



<?php
//display names of files in the folder
if(isset($_POST["showFiles"])) {
    $path = $_POST["path"];
    echo "<p>Your folder path: <em> $path </em></p>";
    echo "Number of files: ";
    echo count(array_diff( scandir("$path"), array(".", "..", "desktop.ini")));
    echo "<p>Drag and drop the items in the order you want.</p>";
    if ($handle = opendir($path)) {
        echo "<div  class='container'>";
        $count = 0;
        while (false !== ($file = readdir($handle))) {
            if ('.' == $file || '..' == $file || "desktop.ini" === $file) continue;
            $count++;
            echo "<div class='draggable' draggable='true'><span>$count. </span>";
            echo "$file";
            echo "</div>";
        }
        echo "</div>";
        echo "<br>";
        closedir($handle);
        echo "<button id='rename'>Number Your Files</button>";
    }
}

//rename files
if(isset($_GET['data'])) {
    $numberedFiles = explode("*", $_GET['data']);
    $path = array_pop($numberedFiles);
    $path = trim($path."/");
    $path = str_replace("file:///", "", $path);
    var_dump($path);
    echo '<br>';
    if ($handle = opendir($path)) {
        while (false !== ($fileName = readdir($handle))) {
            if ('.' == $fileName || '..' == $fileName || "desktop.ini" === $fileName) continue;
            foreach ($numberedFiles as $numFile) {
                if (strpos($numFile, $fileName) !== false) {
                    rename($path.$fileName, $path.$numFile);
                }
            }
        }
        closedir($handle);
    }
    echo '<pre>';
    print_r($numberedFiles);
    echo '</pre>';
}
?>
    

<script>
//drag and sort functionality
var draggables = document.querySelectorAll('.draggable')
const containers = document.querySelectorAll('.container')

function numberFiles() {
  var numAll = document.querySelectorAll("div > span");
  numAll.forEach((item, index) => {
    item.innerHTML = index + 1 + '. '
  })
}

draggables.forEach(draggable => {
  draggable.addEventListener('dragstart', () => {
    draggable.classList.add('dragging')
  })

  draggable.addEventListener('dragend', () => {
    draggable.classList.remove('dragging')
  })
})

containers.forEach(container => {
  container.addEventListener('dragover', e => {
    e.preventDefault()
    const afterElement = getDragAfterElement(container, e.clientY)
    const draggable = document.querySelector('.dragging')
    if (afterElement == null) {
      container.appendChild(draggable)
      numberFiles()
    } else {
      container.insertBefore(draggable, afterElement)
      numberFiles()
    }
  })
})

function getDragAfterElement(container, y) {
  const draggableElements = [...container.querySelectorAll('.draggable:not(.dragging)')]
  return draggableElements.reduce((closest, child) => {
    const box = child.getBoundingClientRect()
    const offset = y - box.top - box.height / 2
    if (offset < 0 && offset > closest.offset) {
      return { offset: offset, element: child }
    } else {
      return closest
    }
  }, { offset: Number.NEGATIVE_INFINITY }).element
}


//send data
var numberedFiles = []
const btnRename = document.getElementById("rename")
btnRename.addEventListener('click', () =>{
var draggables = document.querySelectorAll('.draggable')
    draggables.forEach((item) =>{
        numberedFiles.push(item.textContent)
    })
    var path = document.querySelector('p > em').textContent;
    numberedFiles.push(path);
    var data = numberedFiles.join('*');
    window.location = 'sort_web_dev.php?data=' + data;
})


//prevent resubmission dialog
window.history.replaceState(null, null, window.location.href);
</script>
</body>
</html>