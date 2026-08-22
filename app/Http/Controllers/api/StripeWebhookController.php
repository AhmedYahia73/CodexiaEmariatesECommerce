<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Stripe\Exception\SignatureVerificationException;
use Stripe\Webhook;

class StripeWebhookController extends Controller
{
    /**
     * Handle incoming Stripe webhook events.
     * Route: POST /api/stripe/webhook  (no auth middleware)
     */
    public function handle(Request $request)
    {
        $payload       = $request->getContent();
        $sigHeader     = $request->header('Stripe-Signature');
        $webhookSecret = config('services.stripe.webhook_secret');

        // ── 1. Verify Stripe signature ────────────────────────────────────
        try {
            $event = Webhook::constructEvent($payload, $sigHeader, $webhookSecret);
        } catch (SignatureVerificationException $e) {
            Log::warning('Stripe webhook signature verification failed: ' . $e->getMessage());
            return response()->json(['error' => 'Invalid signature'], 400);
        } catch (\UnexpectedValueException $e) {
            Log::warning('Stripe webhook invalid payload: ' . $e->getMessage());
            return response()->json(['error' => 'Invalid payload'], 400);
        }

        // ── 2. Handle relevant events ─────────────────────────────────────
        $paymentIntent = $event->data->object ?? null;

        switch ($event->type) {

            // ✅ Payment succeeded → approve the order
            case 'payment_intent.succeeded':
                if ($paymentIntent) {
                    $this->approveOrder($paymentIntent->id);
                }
                break;

            // ❌ Payment failed → keep as faild (already default), just log
            case 'payment_intent.payment_failed':
                if ($paymentIntent) {
                    Log::info('Stripe payment failed for transaction: ' . $paymentIntent->id);
                    // payment_status stays "faild" (set at order creation)
                }
                break;

            // All other events → ignore safely
            default:
                break;
        }

        // Stripe requires 200 response to stop retrying
        return response()->json(['status' => 'ok'], 200);
    }

    /**
     * Find the order by transaction_id and mark as approved.
     */
    private function approveOrder(string $transactionId): void
    {
        $order = Order::where('transaction_id', $transactionId)->first();

        if (!$order) {
            Log::warning('Stripe webhook: Order not found for transaction_id: ' . $transactionId);
            return;
        }

        if ($order->payment_status === 'approve') {
            // Already approved, skip (idempotency)
            return;
        }

        $order->update(['payment_status' => 'approve']);

        Log::info('Stripe webhook: Order #' . $order->id . ' approved. transaction_id: ' . $transactionId);
    }
}
