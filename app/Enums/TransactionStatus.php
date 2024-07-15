<?php
    namespace App\Enums;

    use App\Traits\{EnumValues, EnumOptions};

    enum TransactionStatus : string {
        use EnumValues;
        use EnumOptions;
        
        case PENDING = "pending";
        case APPROVED = "approved";
        case DECLINED = "declined";
        case PROCESSING = "processing";
        case COMPLETED = "completed";
        case CANCELLED = "cancelled";

    }