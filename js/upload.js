//////////////////////////////////////////GLobal variables declaration and inicialization/////////////////////////////////////////////
var files = [],
    approvedFiles = [],
    metadados, cntFiles = 0,
    cntExec = 0,
    cntApproved = 0,
    fsButton = document.getElementById('fs-button'),
    fileSelect = document.getElementById('file-select'),
    uploadBtn = document.getElementById('upload-button'),
    fileForm = document.getElementById('file-form'),
    pgBar = document.getElementById('pgBar'),
    approvedLbl = document.getElementById('approvedLbl'),
    statusDiv = document.getElementById('status'),
    statusLbl = document.getElementById('statusLbl');
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////User interaction events//////////////////////////////////////////////////////
fsButton.onclick = function() {
    fileSelect.click();
};
fileSelect.onchange = function() {
    var fileInput = this;
    var fFiles = fileInput.files;
    initFilesAnalysis(fFiles);
};
fileForm.onsubmit = function(event) {
    event.preventDefault();
    uploadBtn.style.display = "none";
    statusDiv.innerHTML = "<h2>Enviando arquivo(s)...</h2>";
    uploadFiles();
};
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////FileDrop////////////////////////////////////////////////////////////////
if (window.FileReader) {
    addEventHandler(window, 'load', function() {
        var drop = document.getElementById('drop');

        function cancel(e) {
            if (e.preventDefault) {
                e.preventDefault();
            }
            return false;
        }
        // Tells the browser that we *can* drop on this target
        addEventHandler(drop, 'dragover', cancel);
        addEventHandler(drop, 'dragenter', cancel);
        addEventHandler(drop, 'drop', function(e) {
            e = e || window.event; // get window.event if e argument missing (in IE)   
            if (e.preventDefault) {
                e.preventDefault();
            } // stops the browser from redirecting off to the image.
            statusDiv.style.display = "inline";
            var dt = e.dataTransfer;
            var fFiles = dt.files;
            //call file analysis function
            initFilesAnalysis(fFiles);
            return false;
        });
    });
} else {
    statusLbl.innerHTML = 'Your browser does not support the HTML5 FileReader.';
}

function addEventHandler(obj, evt, handler) {
    if (obj.addEventListener) {
        // W3C method
        obj.addEventListener(evt, handler, false);
    } else if (obj.attachEvent) {
        // IE method.
        obj.attachEvent('on' + evt, handler);
    } else {
        // Old school method.
        obj['on' + evt] = handler;
    }
}
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////Utilitary functions//////////////////////////////////////////////////////
function normalizeGPS(coordA, coordB, refA, refB) {
    var strNorm, str1, str2;
    try {
        str1 = coordA[0] + "º" + coordA[1] + "'" + coordA[2] + '"' + refA;
        str2 = coordB[0] + "º" + coordB[1] + "'" + coordB[2] + '"' + refB;
        strNorm = str1 + ',' + str2;
    } catch (e) {
        alert(e);
    }
    return strNorm;
}

function getEXIFImageDimensions(image, exifObject) {
    var dimensions = [];
    var width = exifObject.getTag(image, "ImageWidth");
    var height = exifObject.getTag(image, "ImageHeight");
    if (width !== undefined && height !== undefined) {
        dimensions[0] = width;
        dimensions[1] = height;
    } else {
        dimensions[0] = exifObject.getTag(image, "PixelXDimension");
        dimensions[1] = exifObject.getTag(image, "PixelYDimension");
    }
    return dimensions;
}
///////////////////////////////////////////////////////////////Core////////////////////////////////////////////////////////////////////////
function initFilesAnalysis(fFiles) {
    //copy data to global variable so they can be acessed by exifCallback() and uploadFiles()
    files = fFiles;
    cntFiles = fFiles.length;
    //inicialize global variables
    cntExec = 0;
    cntApproved = 0;
    metadados = "";
    //
    statusDiv.style.display = "inline";
    statusLbl.innerHTML = "Status: Analisando arquivos...";
    approvedLbl.innerHTML = "";
    for (var i = 0; i < cntFiles; i++) {
        EXIF.getData(fFiles[i], exifCallback);
    }
}

function uploadFiles() {
    var cnt = files.length;
    var url = fileForm.action;
    if (cnt > 0) {
        var formData = new FormData();
        // Loop through each of the selected files.
        for (var i = 0; i < cnt; i++) {
            var file = files[i];
            // Check the file type.
            if (!file.type.match('image.*')) {
                continue;
            }
            formData.append('photos[]', file, file.name);
        }
        var xhr = new XMLHttpRequest();
        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4) { // request is done
                if (xhr.status === 200) { // successfully
                    statusDiv.innerHTML = xhr.responseText;
                } else if (xhr.status == 404) {
                    alert('Page not found: ' + url);
                } else {
                    alert('uploadFiles: An error occurred!');
                }
            }
        };
        xhr.open('POST', url, true); //true = does not wait until complete
        xhr.send(formData);
    }
}
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////Callbacks////////////////////////////////////////////////////////
var exifCallback = function() {
    cntExec++;
    //atualiza barra de progresso
    pgBar.value = cntExec / cntFiles * 100;
    //obtem tags jpg exif dos arquivos
    var gpslat = EXIF.getTag(this, "GPSLatitude");
    var gpslong = EXIF.getTag(this, "GPSLongitude");
    var gpslatref = EXIF.getTag(this, "GPSLatitudeRef");
    var gpslongref = EXIF.getTag(this, "GPSLongitudeRef");
    var imageDimensions = getEXIFImageDimensions(this, EXIF);
    var imageWidth = imageDimensions[0];
    var imageHeight = imageDimensions[1];
    var dateTime = EXIF.getTag(this, "DateTime");
    var orientation = EXIF.getTag(this, "Orientation");
    var make = EXIF.getTag(this, "Make");
    var software = EXIF.getTag(this, "Software");
    var model = EXIF.getTag(this, "Model");
    var artist = EXIF.getTag(this, "Artist");
    var flash = EXIF.getTag(this, "Flash");
    var sceneCaptureType = EXIF.getTag(this, "SceneCaptureType");
    if (gpslat !== undefined && gpslong !== undefined && gpslatref !== undefined && gpslongref !== undefined) {
        var strGps = normalizeGPS(gpslat, gpslong, gpslatref, gpslongref);
        approvedFiles[cntApproved] = this;
        cntApproved++;
        metadados += "<br><br>Arquivo:" + this.name + "<br>Tamanho:" + this.size + "bytes" + "<br>Data Criação:" + dateTime + "<br>Orientação:" + orientation + "<br>Largura:" + imageWidth + "<br>Altura:" + imageHeight + "<br>Fabricante:" + make + "<br>Software:" + software + "<br>Modelo da Câmera:" + model + "<br>Artista:" + artist + "<br>Flash:" + flash + "<br>Tipo de Captura de Cena:" + sceneCaptureType + "<br>Coord. GPS:" + strGps + "<br>Posição geográfica:<a href=http://www.google.com/maps/place/" + strGps + ">Mapa<a/>";
    }
    //verifica se analise esta concluida
    if (cntExec == cntFiles) {
        if (cntApproved > 0) {
            uploadBtn.style.display = "inline";
            if (cntApproved == 1) {
                approvedLbl.innerHTML = "Arquivo aprovado:" + metadados;
                statusLbl.innerHTML = "Status: Arquivo pronto para upload";
            } else {
                approvedLbl.innerHTML = "Arquivos aprovados(" + cntApproved + "):" + metadados;
                statusLbl.innerHTML = "Status: Arquivos prontos para upload";
            }
        } else {
            uploadBtn.style.display = "none";
            statusLbl.innerHTML = "Status: Nenhum arquivo aprovado";
        }
    }
};
