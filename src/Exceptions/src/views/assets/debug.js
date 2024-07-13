let wpfDebugBar = document.querySelector('#wpfDebugBar');
wpfDebugBar.style.display = 'block';

function wpfVisualErrorShowHide()
{
    if(wpfDebugBar.style.display == 'none')
    {
        wpfDebugBar.style.display = 'block';
    } else {
        wpfDebugBar.style.display = 'none';
    }
}

function filterContentPerLinePC(element, highlightLine, addLine = false, numStart = 0) {
    var codeLines = element.innerHTML.split('\n');
    let contentPerLine = '';
    let lineNumberStart = numStart;
    for (var i = 0; i < codeLines.length; i++) {
        lineNumberStart++;
        let newLine = codeLines.length  > (i + 1) ? '\n' : ''; 
        let contentLine = codeLines[i].trim() == '' ? '&nbsp;' : codeLines[i];
        let setClassHighlight = lineNumberStart == highlightLine ? ' hljs-line-highlight-code' : '';
        contentPerLine += '<span class="cp-per-line' + setClassHighlight + '">' + contentLine + '</span>' + newLine;
    }

    element.innerHTML = contentPerLine;

    if(addLine){
        addLineNumbers(element, numStart, highlightLine);
    }

    document.querySelectorAll('.cp-per-line').forEach(element => {
        if (element.innerText.trim() === '') {
            element.innerHTML = '&nbsp;';
        }
    });
}

function addLineNumbers(element, numStart, highlightLine) {
    var codeLines = element.innerHTML.split('\n');
    var lineNumberHtml = '';

    for (var i = 0; i < codeLines.length; i++) {
        numStart++;
        let setClassHighlight = numStart == highlightLine ? ' hljs-line-highlight' : '';
        lineNumberHtml += '<span class="line-number' + setClassHighlight + '">' + (numStart) + '</span>';
    }

    element.innerHTML = '<span class="line-numbers">' + lineNumberHtml + '</span>' + element.innerHTML;
}

function highlightLine(element, lineNumber, topNum) {
    let numStart = topNum;
    // add line numbers
    if (lineNumber > 0 && lineNumber <= element.textContent.split('\n').length) {
        var spansCode = element.querySelectorAll('span.code');
        spansCode.forEach((span) => {
            span.classList.remove('hljs-line-highlight');
        });

        var lines = element.querySelectorAll('span.line-number');
        lines[lineNumber - 1].classList.add('hljs-line-highlight');
    }

    // add mark to code
    var spans = element.querySelectorAll('span');

    spans.forEach((spanItem, index) => {
        spanItem.classList.remove('hljs-line-highlight-code');
    });

    var codeLines = element.innerHTML.split('\n');
    codeLines[lineNumber - 1] = '<span class="hljs-line-highlight-code">' + codeLines[lineNumber - 1] + '</span>';
    element.innerHTML = codeLines.join('\n');
}

function showAllErrMessageInfo() {
    let getShowErrMessageStatus = document.querySelector('#limit-show-error-info-id');
    let getShowErrMessageAttr = getShowErrMessageStatus.getAttribute('show-error-method');
    let setToggleTextMsg = document.querySelector('.bar-error-message-toggle');
    if(getShowErrMessageAttr == 'limit'){
        getShowErrMessageStatus.setAttribute('show-error-method', 'all');
        setToggleTextMsg.textContent = 'Show Less';
    } else {
        getShowErrMessageStatus.setAttribute('show-error-method', 'limit');
        setToggleTextMsg.textContent = 'Show All';
    }
}

function showStackTraceItem(stackTraceId, filePath, lineNumber) {
    
    // set all display none
    let cpItem = document.querySelectorAll('.cp-item');
    for(let i = 0; i < cpItem.length; i++) {
        cpItem[i].style.display = 'none';
    }

    // show item selected
    showItemStItem = document.querySelector('#code-preview-' + stackTraceId);
    showItemStItem.style.display = 'block';

    // set all tab no active
    let tabAllSt = document.querySelectorAll('.stack-trace-item');
    for(let i = 0; i < tabAllSt.length; i++) {
        tabAllSt[i].classList.remove('active');
    }

    // set one tab active
    let setTabActive = document.querySelector('#stack_trace_' + stackTraceId);
    setTabActive.classList.add('active');

    // set file path & line number
    let setFilePath = document.querySelector('#cp-file-path');
    let setLine = document.querySelector('#cp-line');

    setFilePath.textContent = filePath;
    setLine.textContent = lineNumber;

}