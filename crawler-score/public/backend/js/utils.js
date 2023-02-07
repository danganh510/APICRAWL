function slugify(text) {
    slug = text.toLowerCase();
    slug = slug.replace(/á|à|ả|ạ|ã|ă|ắ|ằ|ẳ|ẵ|ặ|â|ấ|ầ|ẩ|ẫ|ậ/gi, 'a');
    slug = slug.replace(/é|è|ẻ|ẽ|ẹ|ê|ế|ề|ể|ễ|ệ/gi, 'e');
    slug = slug.replace(/i|í|ì|ỉ|ĩ|ị/gi, 'i');
    slug = slug.replace(/ó|ò|ỏ|õ|ọ|ô|ố|ồ|ổ|ỗ|ộ|ơ|ớ|ờ|ở|ỡ|ợ/gi, 'o');
    slug = slug.replace(/ú|ù|ủ|ũ|ụ|ư|ứ|ừ|ử|ữ|ự/gi, 'u');
    slug = slug.replace(/ý|ỳ|ỷ|ỹ|ỵ/gi, 'y');
    slug = slug.replace(/đ/gi, 'd');
    slug = slug.replace(/\`|\~|\!|\@|\#|\||\$|\%|\^|\&|\*|\(|\)|\+|\=|\,|\.|\/|\?|\>|\<|\'|\"|\:|\;|_/gi, '');
    slug = slug.replace(/ /gi, "-");
    slug = slug.replace(/\-\-\-\-\-/gi, '-');
    slug = slug.replace(/\-\-\-\-/gi, '-');
    slug = slug.replace(/\-\-\-/gi, '-');
    slug = slug.replace(/\-\-/gi, '-');
    slug = '@' + slug + '@';
    slug = slug.replace(/\@\-|\-\@|\@/gi, '');
    slug = slug.replace(/“|”/gi, '');
    return slug.toString().toLowerCase()
        .replace(/\s+/g, '-')
        .replace(/[^\w\-]+/g, '')
        .replace(/\-\-+/g, '-')
        .replace(/^-+/, '')
        .replace(/-+$/, '');
}

$(document).ready(function () {
    $(window).keydown(function (event) {
        if (event.keyCode == 13) {
            event.preventDefault();
            return false;
        }
    });
    $('#checkboxAll').change(function () {
        $('.check').prop("checked", $(this).prop('checked'));
    });
    var total_checkbox = 0;
    $('.check').each(function () {
        total_checkbox++;
    });
    $('.check').change(function () {
        var count = 0;
        $('.check').each(function (index, item) {
            if ($(item).is(':checked')) count++;
        });
        if (count == total_checkbox) {
            $('#checkboxAll').prop('checked', true);
            count = 0;
        } else {
            $('#checkboxAll').prop('checked', false);
        }
    });
    $('#slcAction').change(function () {
        var value = $(this).val();
        var checked = [];
        if (value == "drop") {
            $('#myModal').modal("show");
            $("input[name='item[]']:checked").each(function () {
                checked.push($(this).val());
            });
            var location_country_code = $("#slcLocationCountry").val();
            if (location_country_code == 'all') {
                $('.content-warning').text('Please choose one location country.');
                $('.delete-all').hide();
            } else if (checked.length === 0) {
                $('.content-warning').text('Please choose at least one item.');
                $('.delete-all').hide();
            } else if (checked.length === 1) {
                $('.content-warning').text('Are you sure to remove ID = ' + checked[0] + '?');
                $('.delete-all').show();
            } else {
                $('.content-warning').text('Are you sure to remove IDs selected?');
                $('.delete-all').show();
            }
        }
        if (value == "active") {
            $('#myModalActive').modal("show");
            $("input[name='item[]']:checked").each(function () {
                checked.push($(this).val());
            });
            if (checked.length === 0) {
                $('.content-warning').text('Please choose at least one item.');
                $('.active-all').hide();
            } else if (checked.length === 1) {
                $('.content-warning').text('Are you sure to active ID = ' + checked[0] + '?');
                $('.active-all').show();
            } else {
                $('.content-warning').text('Are you sure to active IDs selected?');
                $('.active-all').show();
            }
        }
        if (value == "inactive") {
            $('#myModalInActive').modal("show");
            $("input[name='item[]']:checked").each(function () {
                checked.push($(this).val());
            });
            if (checked.length === 0) {
                $('.content-warning').text('Please choose at least one item.');
                $('.inactive-all').hide();
            } else if (checked.length === 1) {
                $('.content-warning').text('Are you sure to inactive ID = ' + checked[0] + '?');
                $('.inactive-all').show();
            } else {
                $('.content-warning').text('Are you sure to inactive IDs selected?');
                $('.inactive-all').show();
            }
        }
    });
    $("#myModal").on("hidden.bs.modal", function () {
        $('#slcAction').val('');
    });
});