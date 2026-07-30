# React / Frontend Payment Integration Guide (Moyasar)

This document provides a step-by-step guide for Frontend (React/Vue/Next.js) developers to integrate the Moyasar payment gateway using our backend APIs.

## 🔗 API Endpoints

**Base URL:** `YOUR_API_BASE_URL/api`
**Headers Required:** `Authorization: Bearer {USER_TOKEN}`

### 1. Create Payment
**Endpoint:** `POST /payments`

**Request Body (JSON):**
```json
{
    "payable_type": "course", // or "service"
    "payable_id": 1, // ID of the course or service
    "amount": 100.00, // Must exact match the course/service price
    "currency": "SAR",
    "description": "Payment for Course #1",
    "callback_url": "https://yourwebsite.com/payment/callback", // 👈 Frontend route for callback
    "source": {
        "type": "creditcard",
        "name": "John Doe",
        "number": "41111111111111111",
        "cvc": "123",
        "month": "12",
        "year": "2025"
    }
}
```

**Response:**
```json
{
    "success": true,
    "message": "Payment request created successfully.",
    "data": {
        "payment": { "id": 1, "status": "pending" },
        "moyasar_payment": {
            "id": "abc-123-xyz",
            "status": "initiated",
            "source": {
                "transaction_url": "https://api.moyasar.com/v1/payments/abc/3d_secure"
            }
        }
    }
}
```

### 2. Verify Payment (After Callback)
**Endpoint:** `POST /payments/{moyasar_payment_id}/verify`
*Call this after the user is redirected back to your website.*

---

## 🛠️ Implementation Flow in React

### Step 1: Call Create API
Make an HTTP POST request to `/payments` using Axios or Fetch. Check the `status` inside `data.moyasar_payment.status`.

* **If `status == 'paid'`**: Payment is successful immediately (No OTP required). Redirect user to the **Success Page**.
* **If `status == 'failed'`**: Payment failed. Show an **Error Alert**.
* **If `status == 'initiated'`**: 3D Secure (OTP) is required. Proceed to **Step 2**.

### Step 2: Redirect to 3D Secure (OTP)
Extract the `transaction_url` from the response (`data.moyasar_payment.source.transaction_url`). Redirect the user's browser to this URL so they can enter their bank OTP.

```javascript
const handlePayment = async () => {
    try {
        const response = await axios.post('/api/payments', paymentData);
        const moyasarPayment = response.data.data.moyasar_payment;
        
        if (moyasarPayment.status === 'paid') {
            navigate('/payment/success');
        } else if (moyasarPayment.status === 'initiated') {
            // Redirect to Bank OTP page
            window.location.href = moyasarPayment.source.transaction_url;
        }
    } catch (error) {
        console.error("Payment failed", error);
    }
};
```

### Step 3: Handle Callback Route
After the user enters the OTP on the bank's page, Moyasar will redirect them back to the `callback_url` you provided (e.g., `https://yourwebsite.com/payment/callback`).

Moyasar will append the payment ID and status to the URL:
`https://yourwebsite.com/payment/callback?id=abc-123-xyz&status=paid&message=Approved`

Create a page in React to handle this route:
```jsx
import { useEffect, useState } from 'react';
import { useSearchParams, useNavigate } from 'react-router-dom';
import axios from 'axios';

const PaymentCallbackPage = () => {
    const [searchParams] = useSearchParams();
    const navigate = useNavigate();
    const [loading, setLoading] = useState(true);

    useEffect(() => {
        const paymentId = searchParams.get('id');
        
        if (paymentId) {
            verifyPayment(paymentId);
        }
    }, []);

    const verifyPayment = async (id) => {
        try {
            // Call backend Verify API
            const response = await axios.post(`/api/payments/${id}/verify`);
            
            if (response.data.success) {
                navigate('/payment/success');
            } else {
                navigate('/payment/failed');
            }
        } catch (error) {
            navigate('/payment/failed');
        } finally {
            setLoading(false);
        }
    };

    return (
        <div>
            {loading ? <p>Verifying your payment, please wait...</p> : null}
        </div>
    );
};

export default PaymentCallbackPage;
```

---

## 🔄 Retry Failed Payments
If a payment fails and you want to retry with a new card, use the Pay API.

**Endpoint:** `POST /payments/pay`
**Request Body:**
```json
{
    "payment_id": "abc-123-xyz", // The Moyasar Gateway ID of the failed payment
    "callback_url": "https://yourwebsite.com/payment/callback",
    "source": { ... } // New card details
}
```
The flow remains exactly the same as creating a new payment.
