$(document).ready(function() {
    
    const MAX_ATTRIBUTES = 3;
    const $attrContainer = $('#attribute-rows-container');
    const $btnAddAttr = $('#btn-add-attribute-row');
    const $btnGenerate = $('#btn-generate-variants');
    const $variantContainer = $('#variant-container');
    const $emptyNotify = $('#empty-variant-notify');
    
    // Bulk Edit Elements
    const $btnApplyBulk = $('#btn-apply-bulk');
    const $bulkPrice = $('#bulk-price');
    const $bulkComparePrice = $('#bulk-compare-price');
    const $bulkStock = $('#bulk-stock');

    const attrTemplate = $('#attribute-select-template').html();
    const variantTemplate = $('#variant-row-template').html();
    let variantIndex = Date.now(); 

    if (typeof allAttributes === 'undefined') {
        console.error('Biến allAttributes chưa được khởi tạo.');
        return;
    }

    // --- HÀM HELPER & RENDER SELECT ---
    function getAttributeOptionsHtml(selectedValue = null) {
        let html = '<option value="">-- Chọn --</option>';
        let selectedAttrIds = [];
        $('.attribute-type-select').each(function() {
            let val = $(this).val();
            if (val && val != selectedValue) selectedAttrIds.push(parseInt(val));
        });

        allAttributes.forEach(attr => {
            if (!selectedAttrIds.includes(attr.id)) {
                let selected = (selectedValue == attr.id) ? 'selected' : '';
                html += `<option value="${attr.id}" ${selected}>${attr.name}</option>`;
            }
        });
        return html;
    }

    function updateAllAttributeSelects() {
        $('.attribute-type-select').each(function() {
            let $currentSelect = $(this);
            let currentVal = $currentSelect.val();
            $currentSelect.html(getAttributeOptionsHtml(currentVal));
            if(currentVal) $currentSelect.val(currentVal);
        });
    }

    // --- THÊM DÒNG THUỘC TÍNH ---
    $btnAddAttr.click(function() {
        if ($('.attribute-row').length >= MAX_ATTRIBUTES) {
            alert('Tối đa ' + MAX_ATTRIBUTES + ' thuộc tính.');
            return;
        }
        let $newRow = $(attrTemplate);
        $attrContainer.append($newRow);
        $newRow.find('.attribute-type-select').html(getAttributeOptionsHtml());
        $newRow.find('.select2').select2({ theme: 'bootstrap4', width: '100%' });
        checkMaxAttributes();
    });

    // --- XỬ LÝ CHANGE ATTRIBUTE ---
    $attrContainer.on('change', '.attribute-type-select', function() {
        let attrId = $(this).val();
        let $row = $(this).closest('.attribute-row');
        let $valueSelect = $row.find('.attribute-value-select');
        
        $valueSelect.empty().prop('disabled', true);

        if (attrId) {
            let attribute = allAttributes.find(a => a.id == attrId);
            if (attribute && attribute.values) {
                let html = '';
                attribute.values.forEach(val => {
                    html += `<option value="${val.id}" data-name="${val.value}">${val.value}</option>`;
                });
                $valueSelect.html(html).prop('disabled', false);
                $valueSelect.trigger('change');
            }
        }
        updateAllAttributeSelects();
    });

    // --- XÓA DÒNG ATTRIBUTE ---
    $attrContainer.on('click', '.btn-remove-attr-row', function() {
        $(this).closest('.attribute-row').remove();
        updateAllAttributeSelects();
        checkMaxAttributes();
    });

    function checkMaxAttributes() {
        $btnAddAttr.prop('disabled', $('.attribute-row').length >= MAX_ATTRIBUTES);
    }

    // ============================================================
    // LOGIC GEN BIẾN THỂ (ĐÃ FIX DUPLICATE & NO CONFIRM)
    // ============================================================
    $btnGenerate.click(function() {
        let attributesData = [];
        let missingSelection = false;

        // 1. Thu thập dữ liệu cấu hình
        $('.attribute-row').each(function() {
            let $row = $(this);
            let attrName = $row.find('.attribute-type-select option:selected').text();
            let attrId = $row.find('.attribute-type-select').val();
            let selectedData = $row.find('.attribute-value-select').select2('data');

            if (attrId) {
                if (selectedData.length === 0) {
                    missingSelection = true;
                } else {
                    let values = selectedData.map(item => ({ id: item.id, name: item.text }));
                    attributesData.push({ attr_id: attrId, attr_name: attrName, values: values });
                }
            }
        });

        if (attributesData.length === 0) { alert('Vui lòng thêm thuộc tính.'); return; }
        if (missingSelection) { alert('Vui lòng chọn giá trị cho tất cả các dòng thuộc tính.'); return; }

        // Số lượng thuộc tính hiện tại (Ví dụ: 2 là Màu, Size)
        let currentAttrCount = attributesData.length;

        // 2. DỌN DẸP CÁC BIẾN THỂ CŨ KHÔNG KHỚP CẤU TRÚC
        // Ví dụ: Bảng đang có biến thể chỉ có "Màu" (1 ID), nhưng giờ cấu hình là "Màu + Size" (2 IDs)
        // -> Xóa các dòng chỉ có 1 ID đi.
        $('#variant-container tr').each(function() {
            let $row = $(this);
            
            // Lấy chuỗi IDs (VD: "1,5")
            let idsStr = $row.find('.variant-attr-ids').val();
            
            if (idsStr) {
                // Đếm số lượng ID
                let idCount = idsStr.split(',').length;

                // Nếu số lượng ID trong dòng cũ KHÁC số lượng thuộc tính đang cấu hình
                // -> Dòng này lỗi thời -> XÓA
                if (idCount !== currentAttrCount) {
                    if ($row.hasClass('existing-variant')) {
                        // Nếu là hàng có trong DB -> Đánh dấu xóa mềm và ẩn
                        $row.find('.delete-flag').val('1');
                        $row.hide();
                    } else {
                        // Nếu là hàng mới tạo bằng JS -> Xóa hẳn khỏi DOM
                        $row.remove();
                    }
                }
            }
        });

        // 3. Tạo tổ hợp mới
        let combinations = cartesian(attributesData);
        
        // 4. Render (Logic cũ)
        renderVariants(combinations);
    });

    function cartesian(attributes) {
        var r = [], arg = attributes, max = arg.length-1;
        function helper(arr, i) {
            for (var j=0, l=arg[i].values.length; j<l; j++) {
                var a = arr.slice(0);
                a.push(arg[i].values[j]);
                if (i==max) r.push(a);
                else helper(a, i+1);
            }
        }
        helper([], 0);
        return r;
    }

    function renderVariants(combinations) {
        let basePrice = $('input[name="price"]').val() || 0;
        let productCode = $('input[name="code"]').val() || 'SKU';
        
        $emptyNotify.hide();

        combinations.forEach(combo => {
            // Tạo chuỗi ID để định danh: "1,5" (Phải sắp xếp để "1,5" == "5,1")
            let attrIdsArray = combo.map(c => parseInt(c.id)).sort((a, b) => a - b);
            let attrIdsString = attrIdsArray.join(',');

            // --- CHECK DUPLICATE ---
            let isDuplicate = false;
            
            // Duyệt qua tất cả các input attribute_value_ids đang có trong bảng
            $('.variant-attr-ids').each(function() {
                if ($(this).val() === attrIdsString) {
                    // Đã tồn tại!
                    let $existingRow = $(this).closest('tr');
                    
                    // Nếu dòng này đang bị ẩn (đã xóa), thì hiện lại và bỏ cờ xóa
                    if (!$existingRow.is(':visible') || $existingRow.find('.delete-flag').val() == '1') {
                        $existingRow.show();
                        $existingRow.find('.delete-flag').val('0');
                    }
                    
                    // Đánh dấu là đã xử lý xong combo này
                    isDuplicate = true;
                    return false; // Break loop each
                }
            });

            // Nếu đã tồn tại thì bỏ qua, không tạo mới
            if (isDuplicate) return; 

            // --- TẠO MỚI NẾU KHÔNG TRÙNG ---
            let variantName = combo.map(c => c.name).join(' - ');
            let skuSuffix = combo.map(c => slugify(c.name)).join('-');
            let sku = productCode + '-' + skuSuffix.toUpperCase();

            let html = variantTemplate
                .replace(/{index}/g, 'new_' + variantIndex)
                .replace(/{name}/g, variantName)
                .replace(/{sku}/g, sku)
                .replace(/{price}/g, basePrice)
                .replace(/{stock}/g, 10) // Mặc định tồn kho là 10
                .replace(/{attr_ids}/g, attrIdsString);

            $variantContainer.append(html);
            variantIndex++;
        });
    }

    // ============================================================
    // LOGIC SỬA HÀNG LOẠT (BULK EDIT)
    // ============================================================
    $btnApplyBulk.click(function() {
        let price = $bulkPrice.val();
        let comparePrice = $bulkComparePrice.val();
        let stock = $bulkStock.val();

        // Chỉ áp dụng cho các dòng đang hiện (visible)
        let $rows = $variantContainer.find('tr:visible');

        if ($rows.length === 0) {
            alert('Không có biến thể nào để áp dụng.');
            return;
        }

        let count = 0;
        $rows.each(function() {
            let $row = $(this);
            // Chỉ update nếu ô input có giá trị (không update nếu để trống)
            if (price !== '') $row.find('input[name$="[price]"]').val(price);
            if (comparePrice !== '') $row.find('input[name$="[compare_at_price]"]').val(comparePrice);
            if (stock !== '') $row.find('input[name$="[stock]"]').val(stock);
            count++;
        });
    });

    function slugify(text) {
        return text.toString().toLowerCase()
            .replace(/à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ/g, "a")
            .replace(/è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ/g, "e")
            .replace(/ì|í|ị|ỉ|ĩ/g, "i")
            .replace(/ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ/g, "o")
            .replace(/ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ/g, "u")
            .replace(/ỳ|ý|ỵ|ỷ|ỹ/g, "y")
            .replace(/đ/g, "d")
            .replace(/\s+/g, '-').replace(/[^\w\-]+/g, '').replace(/\-\-+/g, '-').replace(/^-+/, '').replace(/-+$/, '');
    }

    // Xóa biến thể
    $variantContainer.on('click', '.btn-remove-variant', function() {
        let row = $(this).closest('tr');
        if (row.hasClass('existing-variant')) {
            row.find('.delete-flag').val('1');
            row.hide();
        } else {
            row.remove();
        }
        if ($variantContainer.find('tr:visible').length === 0) $emptyNotify.show();
    });
    // ============================================================
    // 5. TỰ ĐỘNG ĐIỀN DỮ LIỆU CŨ (KHI EDIT)
    // ============================================================
    function initExistingData() {
        // Kiểm tra xem có dữ liệu cũ truyền từ Controller không
        if (typeof usedAttributes !== 'undefined' && Array.isArray(usedAttributes) && usedAttributes.length > 0) {
            
            usedAttributes.forEach(function(attrData) {
                // 1. Giả lập bấm nút "Thêm thuộc tính"
                // Lưu ý: Chúng ta copy logic của sự kiện click #btn-add-attribute-row
                // nhưng ko gọi click() trực tiếp để kiểm soát tốt hơn các tham số.
                
                if ($('.attribute-row').length >= MAX_ATTRIBUTES) return;

                let $newRow = $(attrTemplate);
                $attrContainer.append($newRow);
                
                // Init Select2
                $newRow.find('.select2').select2({ theme: 'bootstrap4', width: '100%' });

                // 2. Điền Options cho Select Loại thuộc tính
                // Lưu ý: Lúc này chưa có dòng nào được chọn giá trị, nên getAttributeOptionsHtml() sẽ trả về full list
                // Chúng ta sẽ fix lại các dòng khác sau.
                $newRow.find('.attribute-type-select').html(getAttributeOptionsHtml());

                // 3. Set giá trị cho Select Loại (VD: Chọn "Màu sắc")
                $newRow.find('.attribute-type-select').val(attrData.id);

                // 4. Trigger change để load các Values tương ứng (VD: Load Đỏ, Xanh)
                // (Chúng ta tái sử dụng logic change đã viết ở trên)
                $newRow.find('.attribute-type-select').trigger('change');

                // 5. Set giá trị cho Select Values (VD: Chọn Đỏ, Xanh)
                // attrData.values là mảng ID [1, 5]
                let $valSelect = $newRow.find('.attribute-value-select');
                $valSelect.val(attrData.values);
                $valSelect.trigger('change'); // Refresh Select2 UI
            });

            // Sau khi loop xong, chạy lại hàm này để disable các option đã chọn ở các dòng
            updateAllAttributeSelects();
            checkMaxAttributes();
        }
    }

    // GỌI HÀM NGAY KHI LOAD
    initExistingData();

});