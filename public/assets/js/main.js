const copyLinkButton = document.getElementById("link-copy-button")

copyLinkButton.addEventListener(("click"),()=>{
    let link = document.getElementById("insight-link")

    let tempInput = document.createElement("textarea")
    tempInput.value = link.innerHTML
    console.log(tempInput.value)
    document.body.appendChild(tempInput)

    // Selecting the text inside the textarea and copy it
    tempInput.select();
    document.execCommand('copy');
    // console.log("copied")

    // Removing the temporary textarea from the DOM
    document.body.removeChild(tempInput);
                
    copyLinkButton.innerHTML = 'Copied!';
    
    // Reseting button text after a short delay
    setTimeout(() => {
      copyLinkButton.innerHTML = 'Copy';
    }, 2000);
})