
/* USED FOR COMMON WEBSITE ACTIONS SUCH AS MENUS */

$(document).ready(function() {
    
    $(".navShowHide").on("click", function() {  // left menu shows and hides upon each click
        var main = $("#mainSectionContainer");
        if($(window).width() >= 808){
            if(main.hasClass("leftPadding")) {
                $("#sideNavContainer").removeClass("sideNavContainer-compress");
                $("#sideNavContainer .navigationItem a").removeClass("sidenav-item-compress");
                $("#sideNavContainer .navigationItem img").removeClass("sidenav-img-compress");
                $("#sideNavContainer .navigationItem").removeClass("sidenav-mainItem-compress");
                $("#sideNavContainer .navigationItem span").removeClass("sidenav-span-compress");
                $("#sideNavContainer .heading").removeClass("sidenav-heading-compress");
            }
            else {
                
                $("#sideNavContainer").addClass("sideNavContainer-compress");
                $("#sideNavContainer .navigationItem a").addClass("sidenav-item-compress");
                $("#sideNavContainer .navigationItem img").addClass("sidenav-img-compress");
                $("#sideNavContainer .navigationItem").addClass("sidenav-mainItem-compress");
                $("#sideNavContainer .navigationItem span").addClass("sidenav-span-compress");
                $("#sideNavContainer .heading").addClass("sidenav-heading-compress");
            }
            main.toggleClass("leftPadding");
        }
        else{
            $("#sideNavContainer").toggleClass("sideNavContainer-expand-sm");
        }
    });
});
$(document).ready(function(){
    $(window).resize(function(){
        if($(window).width()>807 && $("#sideNavContainer").hasClass("sideNavContainer-expand-sm")){
            $('#sideNavContainer').removeClass('sideNavContainer-expand-sm');
        }
    });
})
$(document).ready(function(){
    $(window).resize(function(){
        if($(window).width()<808 && $("#sideNavContainer").hasClass("sideNavContainer-compress")){
            $("#sideNavContainer").removeClass("sideNavContainer-compress");
            $("#sideNavContainer .navigationItem a").removeClass("sidenav-item-compress");
            $("#sideNavContainer .navigationItem img").removeClass("sidenav-img-compress");
            $("#sideNavContainer .navigationItem").removeClass("sidenav-mainItem-compress");
            $("#sideNavContainer .navigationItem span").removeClass("sidenav-span-compress");
            $("div.navigationItem").eq(1).removeClass("navitem-display-none");
            $("div.navigationItem").eq(3).removeClass("navitem-display-none");
            $("#sideNavContainer .heading").removeClass("sidenav-heading-compress");
            $("#mainSectionContainer").removeClass("leftPadding");
        }
    });
})
$(document).ready(function(){
    $(window).resize(function(){
        if($(window).width()<=662){
            $("#searchIcon").removeClass("navitem-display-none");
            $("#mastHeadContainer .searchBarContainer").addClass("navitem-display-none");
            $("#mastHeadContainer .searchBarContainer").addClass("searchbar-margin");
        }
    });
})
$(document).ready(function(){
    $(window).resize(function(){
        if($(window).width()>662){
            $("#searchIcon").addClass("navitem-display-none");
            $("#mastHeadContainer .searchBarContainer").removeClass("navitem-display-none");
            $(".navShowHide").removeClass("navitem-display-none");
            $("#mastHeadContainer .logoContainer").removeClass("navitem-display-none");
            $("#mastHeadContainer .rightIcons").removeClass("navitem-display-none");
            $("#backIcon").addClass("navitem-display-none");
            $("#mastHeadContainer .searchBarContainer").removeClass("searchbar-margin");
        }
    });
})


// JS for search bar animations:: STARTS
$(document).ready(function(){
    $("#searchIcon").on("click", function(){
        $("#searchIcon").addClass("navitem-display-none");
        $("#mastHeadContainer .searchBarContainer").removeClass("navitem-display-none");
        $(".navShowHide").addClass("navitem-display-none");
        $("#mastHeadContainer .logoContainer").addClass("navitem-display-none");
        $("#mastHeadContainer .rightIcons").addClass("navitem-display-none");
        $("#backIcon").removeClass("navitem-display-none");
    })
    $("#backIcon").on("click", function(){
        $("#searchIcon").removeClass("navitem-display-none");
        $("#mastHeadContainer .searchBarContainer").addClass("navitem-display-none");
        $(".navShowHide").removeClass("navitem-display-none");
        $("#mastHeadContainer .logoContainer").removeClass("navitem-display-none");
        $("#mastHeadContainer .rightIcons").removeClass("navitem-display-none");
        $("#backIcon").addClass("navitem-display-none");
    })
})

function notSignedIn() {
    alert("You must be signed in to perform this action");
}



$(document).ready(function(){
    $("#selectAll").change(function(){
        alert($('#'))
        $('#categoryList input[type="checkbox"]').prop('checked', this.checked);
    })
})
$(document).ready(function(){
    $("#checkAll").change(function(){
        $('#categoryForm input[type="checkbox"]').prop('checked', this.checked);
    })
})

// function checkRoles(el, formElement){
//     // if ($("#"+el+":checkbox:checked").length > 0)
//     // if ($(el).prop("checked"))
//     {
//         $('#'+formElement+' input[type="checkbox"]').prop('checked', this.checked);
//     }
// }


$(document).ready(function(){
    $("#profileBtn").click(function(){
        $("#profileDropdown").toggle();
    })
})

$(document).ready(function(){
    $("#removeListItem").click(function(){
        if ($("#categoryForm input:checkbox:checked").length > 0){
            // any one is checked
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $('#deleteCategory').click();
                        Swal.fire(
                            'Deleted!',
                            'Your file has been deleted.',
                            'success'
                        )
                    }
                })
        }
        else{
        // none is checked
            Swal.fire(
                'Error!',
                'Please select a category.',
                'warning'
            )
        }
    })
})


function deletePrivilege(formElement, submitBtn){
    if ($("#"+formElement+" input:checkbox:checked").length > 0){
        // any one is checked
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $("#"+submitBtn).click();
                    Swal.fire(
                        'Deleted!',
                        'Your file has been deleted.',
                        'success'
                    )
                }
            })
    }
    else{
    // none is checked
        Swal.fire(
            'Error!',
            'Please select a category.',
            'warning'
        )
    }
}

// $(document).ready(function(){
//     $("#deletePrivilegeFalse").click(function(){
//         if ($("#editModalForm input:checkbox:checked").length > 0){
//             // any one is checked
//             Swal.fire({
//                 title: 'Are you sure?',
//                 text: "You won't be able to revert this!",
//                 icon: 'warning',
//                 showCancelButton: true,
//                 confirmButtonColor: '#3085d6',
//                 cancelButtonColor: '#d33',
//                 confirmButtonText: 'Yes, delete it!'
//                 }).then((result) => {
//                     if (result.isConfirmed) {
//                         $('#deletePrivilegesTrue').click();
//                         Swal.fire(
//                             'Deleted!',
//                             'Your file has been deleted.',
//                             'success'
//                         )
//                     }
//                 })
//         }
//         else{
//         // none is checked
//             Swal.fire(
//                 'Error!',
//                 'Please select a category.',
//                 'warning'
//             )
//         }
//     })
// })


function removeCategory(x){
    Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $(x).click();
                Swal.fire(
                    'Deleted!',
                    'Your file has been deleted.',
                    'success'
                )
            }
        })
}

function passValue(val){
    $(".categoryUserId").val(val);
    // document.cookie = "username="+val;
}

function addRoles(){
    if ($("#roleForm input:checkbox:checked").length > 0){
        Swal.fire({
            icon: 'success',
            title: 'Uploaded successfully',
            showConfirmButton: false,
            timer: 50000
        })
        $("#falseAddBtn").click();
    }
    else{
        Swal.fire(
            'Error!',
            'Please select a category.',
            'warning'
        )
    }
    
}

function checkError(el, el2){
    if ($(el).is(":checked")){
        $("."+el2).prop('disabled', false);
    }
    else{
        $("."+el2).prop('disabled', true);
    }
}

// assignRoles checkAll----------->
$(document).ready(function(){
    $("#checkAllRoles").change(function(){
        if($("#checkAllRoles").prop('checked') == true){
            if($("#roleForm input[type='checkbox']").prop('disabled', true).length > 0){
                $("#roleForm input[type='checkbox']").prop('disabled', false);
            }
            $('#roleForm input[type="checkbox"]').prop('checked', this.checked);
        }
        else{
            $('#roleForm .disabled2').prop('disabled', true);
            $('#roleForm input[type="checkbox"]').prop('checked', false);
        }
    })
})


// Experiment
$(document).on('click', 'input[type="checkbox"][data-group]', function(event) {
    // The checkbox that was clicked
    var actor = $(this);
    // The status of that checkbox
    var checked = actor.prop('checked');
    // The group that checkbox is in
    var group = actor.data('group');
    // All checkboxes of that group
    var checkboxes = $('input[type="checkbox"][data-group="' + group + '"]');
    // All checkboxes excluding the one that was clicked
    var otherCheckboxes = checkboxes.not(actor);
    // Check those checkboxes
    otherCheckboxes.prop('checked', checked);
});
// $(document).ready(function(){
//     if ($('#roleForm .checkboxRow .disabled2').is(":checked")){
//         $('#roleForm .checkboxRow .disabled2').closest(".activeAccess").prop('checked', this.checked);
//     }
//     else{
//         $('#roleForm .checkboxRow .disabled2').closest(".activeAccess").prop('checked', false);
//     }
// })

function addRoles(){
    if(($("#roleForm .checkboxRow td:first-child").prop('checked', true)) && ($("#roleForm .checkboxRow .disabled2").prop('checked', false).length == 2)){
        Swal.fire(
            'Error!',
            'Please select access.',
            'warning'
        )
    }
    else{
        if ($("#roleForm input:checkbox:checked").length > 0){
            Swal.fire({
                icon: 'success',
                title: 'Uploaded successfully',
                showConfirmButton: false,
                timer: 50000
            })
            $("#falseAddBtn").click();
        }
        else{
            Swal.fire(
                'Error!',
                'Please select a category.',
                'warning'
            )
        }
    }
    
}


function editAction(currentEl,label,save,cancel){
    $(currentEl).attr("type", 'text');
    $(label).hide();
    $(save).show();
    $(cancel).show();
}

function cancelAction(currentEl,label,save,cancel){
    $(currentEl).attr("type", 'checkbox');
    $(label).show();
    $(save).hide();
    $(cancel).hide();
}

$(window).click(function(event){
    var $target = $(event.target);
    if(!$target.closest('#profileBtn').length && $("#profileBtn").is(":visible")){
        $("#profileDropdown").hide();
    }
})

function plusMinus(x){
        if($(x).find('svg').attr('data-icon') == 'minus'){
            $(x).find('svg').attr('data-icon','plus');
        }
        else{
            $(x).find('svg').attr('data-icon','minus');
        }
}
