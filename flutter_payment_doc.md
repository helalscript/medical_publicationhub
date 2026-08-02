# Flutter Payment Integration Guide (Moyasar)

This document provides a step-by-step guide for Flutter developers to integrate the Moyasar payment gateway using our backend APIs.

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
    "callback_url": "myapp://payment/callback", // 👈 Use Deep Link for Flutter
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
*Call this after the user returns to the app from the WebView.*

---

## 🛠️ Implementation Flow in Flutter

### Step 1: Call Create API
Make an HTTP POST request to `/payments`. Check the `status` inside `data.moyasar_payment.status`.

* **If `status == 'paid'`**: Payment is successful immediately (No OTP required). Show **Success Screen**.
* **If `status == 'failed'`**: Payment failed. Show **Error Screen**.
* **If `status == 'initiated'`**: 3D Secure (OTP) is required. Proceed to **Step 2**.

### Step 2: Open WebView for 3D Secure (OTP)
Extract the `transaction_url` from the response (`data.moyasar_payment.source.transaction_url`). Open a WebView inside the app so the user can enter their bank OTP.

**Example using `webview_flutter`:**
```dart
import 'package:webview_flutter/webview_flutter.dart';

class PaymentWebView extends StatefulWidget {
  final String transactionUrl;
  
  PaymentWebView({required this.transactionUrl});

  @override
  _PaymentWebViewState createState() => _PaymentWebViewState();
}

class _PaymentWebViewState extends State<PaymentWebView> {
  late WebViewController _controller;

  @override
  void initState() {
    super.initState();
    _controller = WebViewController()
      ..setJavaScriptMode(JavaScriptMode.unrestricted)
      ..setNavigationDelegate(
        NavigationDelegate(
          onNavigationRequest: (NavigationRequest request) {
            // STEP 3: Intercept Callback URL
            if (request.url.startsWith('myapp://payment/callback')) {
              _handlePaymentCallback(request.url);
              return NavigationDecision.prevent; // Stop loading and close webview
            }
            return NavigationDecision.navigate;
          },
        ),
      )
      ..loadRequest(Uri.parse(widget.transactionUrl));
  }

  void _handlePaymentCallback(String url) {
    Uri uri = Uri.parse(url);
    String? paymentId = uri.queryParameters['id'];
    String? status = uri.queryParameters['status'];
    
    // Close WebView
    Navigator.pop(context);
    
    // Proceed to Step 4
    _verifyPayment(paymentId);
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: Text('3D Secure Verification')),
      body: WebViewWidget(controller: _controller),
    );
  }
}
```

### Step 3 & 4: Intercept URL & Verify
As shown in the code above, listen for the `callback_url` (`myapp://payment/callback`). When Moyasar redirects to it, grab the `id` from the URL parameters.

Call the backend Verify API:
```dart
Future<void> _verifyPayment(String? paymentId) async {
    if (paymentId == null) return;
    
    // Call backend API: POST /api/payments/{paymentId}/verify
    var response = await http.post(Uri.parse('YOUR_API/payments/$paymentId/verify'));
    
    // Show Success or Error Screen based on backend response
}
```

---

## 🔄 Retry Failed Payments
If a payment fails and you want to retry with a new card, use the Pay API.

**Endpoint:** `POST /payments/pay`
**Request Body:**
```json
{
    "payment_id": "abc-123-xyz", // The Moyasar Gateway ID of the failed payment
    "callback_url": "myapp://payment/callback",
    "source": { ... } // New card details
}
```
The flow remains exactly the same as creating a new payment.
