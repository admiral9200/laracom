<?php

namespace App\Shop\Products\Repositories;

use App\Shop\AttributeValues\AttributeValue;
use App\Shop\Base\BaseRepository;
use App\Shop\ProductAttributes\ProductAttribute;
use App\Shop\ProductImages\ProductImage;
use App\Shop\Products\Exceptions\ProductInvalidArgumentException;
use App\Shop\Products\Exceptions\ProductNotFoundException;
use App\Shop\Products\Product;
use App\Shop\Products\Repositories\Interfaces\ProductRepositoryInterface;
use App\Shop\Products\Transformations\ProductTransformable;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ProductRepository extends BaseRepository implements ProductRepositoryInterface
{
    use ProductTransformable;

    /**
     * ProductRepository constructor.
     * @param Product $product
     */
    public function __construct(Product $product)
    {
        parent::__construct($product);
        $this->model = $product;
    }

    /**
     * List all the products
     *
     * @param string $order
     * @param string $sort
     * @param array $columns
     * @return Collection
     */
    public function listProducts(string $order = 'id', string $sort = 'desc', array $columns = ['*']) : Collection
    {
        return $this->all($columns, $order, $sort);
    }

    /**
     * Create the product
     *
     * @param array $params
     * @return Product
     */
    public function createProduct(array $params) : Product
    {
        try {
            $product = new Product($params);
            $product->save();
            return $product;
        } catch (QueryException $e) {
            throw new ProductInvalidArgumentException($e->getMessage());
        }
    }

    /**
     * Update the product
     *
     * @param array $params
     * @param int $id
     * @return bool
     */
    public function updateProduct(array $params, int $id) : bool
    {
        try {
            return $this->update($params, $id);
        } catch (QueryException $e) {
            throw new ProductInvalidArgumentException($e->getMessage());
        }
    }

    /**
     * Find the product by ID
     *
     * @param int $id
     * @return Product
     */
    public function findProductById(int $id) : Product
    {
        try {
            return $this->transformProduct($this->findOneOrFail($id));
        } catch (ModelNotFoundException $e) {
            throw new ProductNotFoundException($e->getMessage());
        }
    }

    /**
     * Delete the product
     *
     * @param Product $product
     * @return bool
     */
    public function deleteProduct(Product $product) : bool
    {
        return $product->delete();
    }

    /**
     * Detach the categories
     */
    public function detachCategories()
    {
        $this->model->categories()->detach();
    }

    /**
     * Return the categories which the product is associated with
     *
     * @return Collection
     */
    public function getCategories() : Collection
    {
        return $this->model->categories()->get();
    }

    /**
     * Sync the categories
     *
     * @param array $params
     */
    public function syncCategories(array $params)
    {
        $this->model->categories()->sync($params);
    }

    /**
     * @param $file
     * @param null $disk
     * @return bool
     */
    public function deleteFile(array $file, $disk = null) : bool
    {
        return $this->update(['cover' => null], $file['product']);
    }

    /**
     * @param string $src
     * @return bool
     */
    public function deleteThumb(string $src) : bool
    {
        return DB::table('product_images')->where('src', $src)->delete();
    }

    /**
     * Get the product via slug
     *
     * @param array $slug
     * @return Product
     */
    public function findProductBySlug(array $slug) : Product
    {
        try {
            return $this->findOneByOrFail($slug);
        } catch (ModelNotFoundException $e) {
            throw new ProductNotFoundException($e->getMessage());
        }
    }

    /**
     * @param string $text
     * @return mixed
     */
    public function searchProduct(string $text) : Collection
    {
        return $this->model->searchProduct($text);
    }

    /**
     * @return mixed
     */
    public function findProductImages() : Collection
    {
        return $this->model->images()->get();
    }

    /**
     * @param UploadedFile $file
     * @return string
     */
    public function saveCoverImage(UploadedFile $file) : string
    {
        return $file->store('products', ['disk' => 'public']);
    }

    /**
     * @param Collection $collection
     * @param Product $product
     * @return Collection
     */
    public function saveProductImages(Collection $collection, Product $product)
    {
        $collection->each(function (UploadedFile $file) use ($product) {
            $filename = $file->store('products', ['disk' => 'public']);
            $productImage = new ProductImage([
                'product_id' => $product->id,
                'src' => $filename
            ]);
            $product->images()->save($productImage);
        });
    }

    /**
     * Associate the product attribute to the product
     *
     * @param ProductAttribute $productAttribute
     * @return ProductAttribute
     */
    public function saveProductAttributes(ProductAttribute $productAttribute) : ProductAttribute
    {
        return $this->model->attributes()->save($productAttribute);
    }

    /**
     * List all the product attributes associated with the product
     *
     * @return Collection
     */
    public function listProductAttributes() : Collection
    {
        return $this->model->attributes()->get();
    }

    /**
     * Delete the attribute from the product
     *
     * @param ProductAttribute $productAttribute
     * @return bool|null
     */
    public function removeProductAttribute(ProductAttribute $productAttribute) : ?bool
    {
        return $productAttribute->delete();
    }

    /**
     * @param ProductAttribute $productAttribute
     * @param \App\Shop\AttributeValues\AttributeValue[] ...$attributeValues
     * @return Collection
     */
    public function saveCombination(ProductAttribute $productAttribute, AttributeValue ...$attributeValues) : Collection
    {
        return collect($attributeValues)->each(function (AttributeValue $value) use ($productAttribute) {
            return $productAttribute->attributesValues()->save($value);
        });
    }

    /**
     * @return Collection
     */
    public function listCombinations() : Collection
    {
        return $this->model->attributes()->map(function (ProductAttribute $productAttribute) {
            return $productAttribute->attributesValues;
        });
    }

    /**
     * @param ProductAttribute $productAttribute
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function findProductCombination(ProductAttribute $productAttribute)
    {
        $values = $productAttribute->attributesValues()->get();

        return $values->map(function (AttributeValue $attributeValue) {
            return $attributeValue;
        })->keyBy(function (AttributeValue $item) {
            return strtolower($item->attribute->name);
        })->transform(function (AttributeValue $value) {
            return $value->value;
        });
    }
}
