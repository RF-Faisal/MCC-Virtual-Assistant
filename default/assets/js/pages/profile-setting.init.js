document.querySelector("#profile-foreground-img-file-input") && document.querySelector("#profile-foreground-img-file-input").addEventListener("change", function () {
    var o = document.querySelector(".profile-wid-img"),
        e = document.querySelector(".profile-foreground-img-file-input").files[0],
        i = new FileReader;
    i.addEventListener("load", function () {
        o.src = i.result
    }, !1), e && i.readAsDataURL(e)
}), document.querySelector("#profile-img-file-input") && document.querySelector("#profile-img-file-input").addEventListener("change", function () {
    var o = document.querySelector(".user-profile-image"),
        e = document.querySelector(".profile-img-file-input").files[0],
        i = new FileReader;
    i.addEventListener("load", function () {
        o.src = i.result
    }, !1), e && i.readAsDataURL(e)
});
var count = 2;

function new_link() {
    count++;
    var o = document.createElement("div"),
        e = '<div class="row"> <div class="col-lg-6"> <div class="mb-3"> <label for="companyName" class="form-label">Organization</label> <input type="text" name="org" class="form-control" id="companyName" placeholder="Organization"> </div> </div><div class="col-lg-6"> <div class="mb-3"> <label for="jobTitle" class="form-label">Job Title</label> <input type="text" class="form-control" id="jobTitle" placeholder="Job Title"> </div> </div><div class="col-lg-6"> <div class="mb-3"> <label for="StartdatInput" class="form-label">Start Date</label> <input type="date" class="form-control" data-provider="flatpickr" id="StartdatInput" data-date-format="d M, Y" data-deafult-date="24 Nov, 2021" placeholder="Select date" value="24 Nov 2021"> </div> </div> <div class="col-lg-6"> <div class="mb-3"> <label for="EnddatInput" class="form-label">End Date</label> <input type="date" class="form-control" data-provider="flatpickr" id="EnddatInput" data-date-format="d M, Y" data-deafult-date="24 Nov, 2021" placeholder="Select date" value="02 Jun 2022"> </div> </div> <div class="hstack gap-2 justify-content-end"><a class="btn btn-success" href="javascript:deleteEl(' + (o.id = count) + ')">Delete</a></div></div>';
    o.innerHTML = document.getElementById("newForm").innerHTML + e, document.getElementById("newlink").appendChild(o), document.querySelectorAll("[data-trigger]").forEach(function (o) {
        new Choices(o, {
            placeholderValue: "This is a placeholder set in the config",
            searchPlaceholderValue: "This is a search placeholder",
            searchEnabled: !1
        })
    })
}

function deleteEl(o) {
    d = document;
    o = d.getElementById(o);
    d.getElementById("newlink").removeChild(o)
}