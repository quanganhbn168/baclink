// Biến toàn cục hứng dữ liệu từ Controller
// Anh nhớ khai báo biến này TRƯỚC khi include file JS này trong blade:
// <script> var allAttributes = @json($attributes); </script>

$(document).ready(function() {
    
    const MAX_ATTRIBUTES = 3;
    const $attrContainer = $('#attribute-rows-container');
    const $btnAddAttr = $('#btn-add-attribute-row');
    const $btnGenerate = $('#btn-generate-variants');
    const $variantContainer = $('#variant-container');
    
    // Template
    const attrTemplate = $('#attribute-select-template').html();
    const variantTemplate = $('#variant-row-template').html();

    let variantIndex = 1000; // Index bắt đầu cao để tránh trùng cũ

    // 1. Hàm render options cho thẻ Select thuộc tính
    function getAttributeOptionsHtml() {
        let html = '<option value="">-- Chọn thuộc tính --</option>';
        if (typeof allAttributes !== 'undefined') {
            allAttributes.forEach(attr => {
                html += `<option value="${attr.id}" data-idx="${attr.id}">${attr.name}</option>`;
            });
        }
        return html;
    }

    // 2. Thêm dòng chọn thuộc tính
    $btnAddAttr.click(function() {
        if ($('.attribute-row').length >= MAX_ATTRIBUTES) {
            alert('Chỉ được phép thêm tối đa ' + MAX_ATTRIBUTES + ' thuộc tính.');
            return;
        }

        let $newRow = $(attrTemplate);
        // Điền options vào select
        $newRow.find('.attribute-type-select').html(getAttributeOptionsHtml());
        
        $attrContainer.append($newRow);
        checkMaxAttributes();
    });

    // 3. Xóa dòng chọn thuộc tính
    $attrContainer.on('click', '.btn-remove-attr-row', function() {
        $(this).closest('.attribute-row').remove();
        checkMaxAttributes();
    });

    function checkMaxAttributes() {
        if ($('.attribute-row').length >= MAX_ATTRIBUTES) {
            $btnAddAttr.prop('disabled', true);
        } else {
            $btnAddAttr.prop('disabled', false);
        }
    }

    // 4. Khi chọn Loại thuộc tính -> Load Values tương ứng
    $attrContainer.on('change', '.attribute-type-select', function() {
        let attrId = $(this).val();
        let $row = $(this).closest('.attribute-row');
        let $valueSelect = $row.find('.attribute-value-select');

        $valueSelect.html('').prop('disabled', true);

        if (attrId && typeof allAttributes !== 'undefined') {
            // Tìm attribute object trong mảng gốc
            let attribute = allAttributes.find(a => a.id == attrId);
            if (attribute && attribute.values) {
                let html = '';
                attribute.values.forEach(val => {
                    html += `<option value="${val.id}" data-name="${val.value}">${val.value}</option>`;
                });
                $valueSelect.html(html).prop('disabled', false);
            }
        }
    });

    // ==========================================================
    // LOGIC TẠO BIẾN THỂ (ALGORITHM)
    // ==========================================================
    $btnGenerate.click(function() {
        // Thu thập dữ liệu từ các dòng attribute
        let attributesData = [];
        let isValid = true;

        $('.attribute-row').each(function() {
            let $row = $(this);
            let attrName = $row.find('.attribute-type-select option:selected').text();
            let attrId = $row.find('.attribute-type-select').val();
            
            // Lấy các values đã chọn (Multi select)
            let selectedOptions = $row.find('.attribute-value-select option:selected');
            
            if (attrId && selectedOptions.length > 0) {
                let values = [];
                selectedOptions.each(function() {
                    values.push({
                        id: $(this).val(),
                        name: $(this).data('name') // VD: Đỏ, Xanh
                    });
                });
                
                attributesData.push({
                    attr_id: attrId,
                    attr_name: attrName,
                    values: values
                });
            }
        });

        if (attributesData.length === 0) {
            alert('Vui lòng chọn ít nhất 1 thuộc tính và giá trị.');
            return;
        }

        // TẠO TỔ HỢP (Cartesian Product)
        // attributesData = [ {values: [A, B]}, {values: [1, 2]} ]
        // Result = [ [A, 1], [A, 2], [B, 1], [B, 2] ]
        let combinations = cartesian(attributesData);

        // Render ra bảng
        if(confirm('Hành động này sẽ tạo thêm ' + combinations.length + ' dòng biến thể. Bạn có chắc chắn?')) {
            renderVariants(combinations);
        }
    });

    // Hàm đệ quy tạo tổ hợp
    function cartesian(attributes) {
        var r = [], arg = attributes, max = arg.length-1;
        function helper(arr, i) {
            for (var j=0, l=arg[i].values.length; j<l; j++) {
                var a = arr.slice(0); // clone arr
                a.push(arg[i].values[j]);
                if (i==max)
                    r.push(a);
                else
                    helper(a, i+1);
            }
        }
        helper([], 0);
        return r;
    }

    function renderVariants(combinations) {
        let basePrice = $('input[name="price"]').val() || 0;
        let productCode = $('input[name="code"]').val() || 'SKU';
        
        combinations.forEach(combo => {
            // combo là mảng các object value: [{id: 1, name: 'Đỏ'}, {id: 5, name: 'XL'}]
            
            // 1. Tạo tên biến thể: "Đỏ - XL"
            let variantName = combo.map(c => c.name).join(' - ');
            
            // 2. Tạo mảng ID values: "1,5" (để lưu DB)
            let attrValueIds = combo.map(c => c.id).join(',');

            // 3. Tạo SKU gợi ý: "MA-SP-DO-XL" (đơn giản hóa thành index để user tự sửa)
            let skuSuffix = combo.map(c => slugify(c.name)).join('-');
            let sku = productCode + '-' + skuSuffix.toUpperCase();

            // 4. Render HTML
            let html = variantTemplate
                .replace(/{index}/g, 'new_' + variantIndex)
                .replace(/{name}/g, variantName)
                .replace(/{sku}/g, sku)
                .replace(/{price}/g, basePrice)
                .replace(/{attr_ids}/g, attrValueIds);

            $variantContainer.append(html);
            variantIndex++;
        });

        $('#empty-variant-notify').hide();
    }

    // Hàm tạo slug đơn giản cho SKU
    function slugify(text) {
        return text.toString().toLowerCase()
            .replace(/\s+/g, '-')     // Replace spaces with -
            .replace(/[^\w\-]+/g, '') // Remove all non-word chars
            .replace(/\-\-+/g, '-')   // Replace multiple - with single -
            .replace(/^-+/, '')       // Trim - from start of text
            .replace(/-+$/, '');      // Trim - from end of text
    }

    // Xóa biến thể
    $variantContainer.on('click', '.btn-remove-variant', function() {
        // Logic xóa giống file trước
        let row = $(this).closest('tr');
        if (row.hasClass('existing-variant')) {
            row.find('.delete-flag').val('1');
            row.hide();
        } else {
            row.remove();
        }
        
        if ($variantContainer.find('tr:visible').length === 0) {
            $('#empty-variant-notify').show();
        }
    });

});