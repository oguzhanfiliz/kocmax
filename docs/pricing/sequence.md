sequenceDiagram
    autonumber
    participant FE as Frontend
    participant API as OrderController
    participant PS as PricingService
    participant PE as PriceEngine
    participant CTD as CustomerTypeDetector
    participant STR as PricingStrategy
    participant CC as CurrencyConversionService
    participant CACHE as Cache/Store

    FE->>API: POST /processCheckoutPayment (cart)
    API->>PS: calculatePrice(variant, qty, user)
    PS->>PE: calculatePrice(..., context)
    PE->>CTD: detect(user, context)
    CTD-->>PE: CustomerType
    PE->>CACHE: remember(cacheKey, 5m)
    alt cache hit
        CACHE-->>PE: PriceResult
    else cache miss
        PE->>STR: canCalculatePrice?(variant, qty, user)
        alt cannot
            PE->>STR: try fallback strategies (B2B→B2C→Guest ...)
        end
        STR->>CC: getPriceInCurrency('TRY')
        STR->>STR: collect discounts (smart, campaign, dealer, bulk,...)
        STR-->>PE: PriceResult
        PE->>CACHE: store PriceResult
    end
    PE-->>PS: PriceResult
    PS-->>API: PriceResult
    API->>API: sum totals, create order and items
    API-->>FE: JSON (totals, order data)

