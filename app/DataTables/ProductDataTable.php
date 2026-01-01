<?php

namespace App\DataTables;

use App\Models\Product;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class ProductDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addColumn('action', function ($row) {
                $editUrl = route('admin.products.edit', $row->id);
                $deleteUrl = route('admin.products.destroy', $row->id);
                $actionBtn = '<a href="' . $editUrl . '" class="btn btn-primary btn-sm">Sửa</a> ';
                $actionBtn .= '<form action="' . $deleteUrl . '" method="POST" style="display:inline-block;">';
                $actionBtn .= csrf_field();
                $actionBtn .= method_field('DELETE');
                $actionBtn .= '<button type="submit" class="btn btn-danger btn-sm" onclick="return confirm(\'Bạn có chắc chắn muốn xóa?\')">Xóa</button>';
                $actionBtn .= '</form>';
                return $actionBtn;
            })
            ->editColumn('image', function ($row) {
                $imageUrl = asset($row->image ?? 'images/setting/no-image.png');
                return '<img src="' . $imageUrl . '" alt="product image" width="50">';
            })
            ->editColumn('category.name', function ($row) {
                return $row->category->name ?? 'N/A';
            })
            ->editColumn('price', function ($row) {
                return number_format($row->price) . '₫';
            })
            ->editColumn('status', function ($row) {
                return $row->status ? '<span class="badge badge-success">Hoạt động</span>' : '<span class="badge badge-danger">Tạm ẩn</span>';
            })
            ->rawColumns(['image', 'status', 'action']) // Cho phép hiển thị HTML trong các cột này
            ->setRowId('id');
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(Product $model): QueryBuilder
    {
        // Sử dụng with() để Eager Loading, tối ưu query
        return $model->newQuery()->with('category');
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('product-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->dom('Bfrtip')
            ->orderBy(1)
            ->selectStyleSingle()
            ->buttons([
                Button::make('excel'),
                Button::make('csv'),
                Button::make('pdf'),
                Button::make('print'),
                Button::make('reset'),
                Button::make('reload')
            ]);
    }

    /**
     * Get the dataTable columns definition.
     */
    public function getColumns(): array
    {
        return [
            Column::make('id')->width(50),
            Column::make('image')->title('Ảnh')->orderable(false)->searchable(false),
            Column::make('name')->title('Tên sản phẩm'),
            Column::make('category.name')->title('Danh mục'), // Truy cập vào tên của relation
            Column::make('price')->title('Giá'),
            Column::make('stock')->title('Tồn kho'),
            Column::make('status')->title('Trạng thái'),
            Column::computed('action')
                  ->exportable(false)
                  ->printable(false)
                  ->width(120)
                  ->addClass('text-center')
                  ->title('Hành động'),
        ];
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'Product_' . date('YmdHis');
    }
}