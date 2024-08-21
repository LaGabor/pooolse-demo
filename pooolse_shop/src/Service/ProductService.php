<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Product;

class ProductService
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    
    public function getProductsWithDiscounts(): array
    {
        $queryBuilder = $this->entityManager->createQueryBuilder();

        $queryBuilder->select('p', 'b', 'bd', 'pd')
            ->from(Product::class, 'p')
            ->leftJoin('p.brand', 'b')
            ->leftJoin('b.discount', 'bd')
            ->leftJoin('p.discount', 'pd')
            ->addSelect('(COALESCE(bd.whole, 0) + COALESCE(pd.whole, 0)) AS whole_discount')
            ->addSelect('(COALESCE(bd.percentage, 0) + COALESCE(pd.percentage, 0)) AS percentage_discount')
            ->addSelect('COALESCE(bd.freeItemTreshold, 0) AS brandFreeCount')
            ->addSelect('COALESCE(pd.freeItemTreshold, 0) AS productFreeCount')
            ->orderBy('p.name', 'ASC');

        $query = $queryBuilder->getQuery();
        $products = $query->getArrayResult();

        return array_map(function($item) {
            $product = $item[0];
            $wholeDiscount = $item['whole_discount'] ?? 0;
            $percentageDiscount = $item['percentage_discount'] ?? 0;
    
            $price = $product['price'];
            $discountedPrice = $price - $wholeDiscount;
            if($percentageDiscount > 0){
                $discountedPrice = $discountedPrice - ($price *  $percentageDiscount);
                $discountedPrice = intval(floor($discountedPrice / 10) * 10);
            }
            
            return [
                'id' => $product['productId'],
                'brand_id' =>  $product['brand']['id'],
                'name' => $product['name'],
                'price' => $price,
                'discount_price' => $discountedPrice,
                'brand' => $product['brand']['name'],
                'pic_route' => $product['imgPath'],
                'brandFreeCount' => $item['brandFreeCount'],
                'productFreeCount' => $item['productFreeCount']
            ];
        }, $products);

    }

    //TODO: Refactor!
    public function getProductsByIdsWithDiscounts(array $productArray): array
    {
        if (empty($productArray)) {
            return ['originalPrice' => 0, 'discountPrice' => 0, 'freeProductCount' => 0];
        }
    
        $productIds = array_column($productArray, 'id');   
        $queryBuilder = $this->entityManager->createQueryBuilder();
        $freeProductCount = 0;
        $freeBrandHelper = [];
        $priceHelper = [];
        $originalPrice = 0;
        $discountPrice = 0;
    
        $queryBuilder->select('p', 'b', 'bd', 'pd')
            ->from(Product::class, 'p')
            ->leftJoin('p.brand', 'b')
            ->leftJoin('b.discount', 'bd')
            ->leftJoin('p.discount', 'pd')
            ->addSelect('(COALESCE(bd.whole, 0) + COALESCE(pd.whole, 0)) AS whole_discount')
            ->addSelect('(COALESCE(bd.percentage, 0) + COALESCE(pd.percentage, 0)) AS percentage_discount')
            ->addSelect('COALESCE(bd.freeItemTreshold, 0) AS brandFreeCount')
            ->addSelect('COALESCE(pd.freeItemTreshold, 0) AS productFreeCount')
            ->where($queryBuilder->expr()->in('p.productId', $productIds))
            ->orderBy('p.name', 'ASC');
    
        $query = $queryBuilder->getQuery();
        $products = $query->getArrayResult();
    
        foreach ($products as $item) {
            $product = $item[0];
            $productId = $product['productId'];
            $brand_id =  $product['brand']['id'];
            $quantity = $productArray[$productId]['quantity'];
    
            $wholeDiscount = $item['whole_discount'] ?? 0;
            $percentageDiscount = $item['percentage_discount'] ?? 0;
    
            $price = $product['price'];
            $discountedPrice = $price - $wholeDiscount;
            $priceHelper[$discountedPrice] = $quantity;
    
            if ($percentageDiscount > 0) {
                $discountedPrice = $discountedPrice - ($price * $percentageDiscount);
                $discountedPrice = intval(floor($discountedPrice / 10) * 10);
            }
    
            $originalPrice += $price * $quantity;
            $discountPrice += $discountedPrice * $quantity;
            $brandFreeCount = $item['brandFreeCount'];
            $productFreeCount = $item['productFreeCount'];
    
            if ($productFreeCount >= $quantity) {
                $freeProductCount += 1;
            }
    
            if ($brandFreeCount > 0) {
                if (!isset($freeBrandHelper[$brand_id])) {
                    $freeBrandHelper[$brand_id] = $quantity;
                    if ($quantity >= $brandFreeCount) {
                        $freeProductCount += 1;
                    }
                } else {
                    $prevQuantity = $freeBrandHelper[$brand_id];
                    $freeBrandHelper[$brand_id] += $quantity;
                    if ($freeBrandHelper[$brand_id] >= $brandFreeCount && $prevQuantity < $brandFreeCount) {
                        $freeProductCount += 1;
                    }
                }
            }
        }

        $freeProductPrice  = 0;
        if($freeProductCount > 0){
            $freeProductPrice =  $this->freeProductPriceCounter($freeProductCount, $priceHelper);
        }
        
       return [
            'originalPrice' => $originalPrice,
            'discountPrice' => ($discountPrice - $freeProductPrice),
        ];
    }

    private function freeProductPriceCounter(int $freeProductCount, array $priceHelper): int
    {
        $totalFreePrice = 0;
        $remainingFreeCount = $freeProductCount;
        ksort($priceHelper);
    
        foreach ($priceHelper as $price => $count) {
            if ($remainingFreeCount <= 0) {
                break;
            }
    
            $freeItems = min($remainingFreeCount, $count);
            $totalFreePrice += $price * $freeItems;
            $remainingFreeCount -= $freeItems;
        }
    
        return $totalFreePrice;
    }

    public function getCartProductsWithDiscounts($productArray): array
    {
        if (empty($productArray)) {
            return [];
        }
        
        $productIds = array_column($productArray, 'id');   
        $queryBuilder = $this->entityManager->createQueryBuilder();
        
        $queryBuilder->select('p', 'b', 'bd', 'pd')
            ->from(Product::class, 'p')
            ->leftJoin('p.brand', 'b')
            ->leftJoin('b.discount', 'bd')
            ->leftJoin('p.discount', 'pd')
            ->addSelect('(COALESCE(bd.whole, 0) + COALESCE(pd.whole, 0)) AS whole_discount')
            ->addSelect('(COALESCE(bd.percentage, 0) + COALESCE(pd.percentage, 0)) AS percentage_discount')
            ->addSelect('COALESCE(bd.freeItemTreshold, 0) AS brandFreeCount')
            ->addSelect('COALESCE(pd.freeItemTreshold, 0) AS productFreeCount')
            ->where($queryBuilder->expr()->in('p.productId', $productIds))
            ->orderBy('p.name', 'ASC');

        $query = $queryBuilder->getQuery();
        $products = $query->getArrayResult();

        return array_map(function($item) use ($productArray) {
            $product = $item[0];
            $productId = $product['productId'];
            $quantity = $productArray[$productId]['quantity'];
            $wholeDiscount = $item['whole_discount'] ?? 0;
            $percentageDiscount = $item['percentage_discount'] ?? 0;
    
            $price = $product['price'];
            $discountedPrice = $price - $wholeDiscount;
            if($percentageDiscount > 0){
                $discountedPrice = $discountedPrice - ($price *  $percentageDiscount);
                $discountedPrice = intval(floor($discountedPrice / 10) * 10);
            }
            
            return [
                'id' => $productId,
                'brand_id' =>  $product['brand']['id'],
                'name' => $product['name'],
                'price' => $price,
                'discount_price' => $discountedPrice,
                'brand' => $product['brand']['name'],
                'price_all' => ($price * $quantity),
                'piece' => $quantity,
                'discount_price_all' => ($discountedPrice * $quantity),
                'pic_route' => $product['pngPath'],
                'brandFreeCount' => $item['brandFreeCount'],
                'productFreeCount' => $item['productFreeCount']
            ];
        }, $products);
    }

}
