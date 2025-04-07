<?php

namespace App\Http\Controllers;

use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Validation\ValidationException;

class NotificationController extends Controller
{
    private NotificationService $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function index(): \Illuminate\Http\JsonResponse
    {
        $notifications = $this->notificationService->getAllNotifications();
        return response()->json($notifications);
    }

    public function show($id): \Illuminate\Http\JsonResponse
    {
        $notification = $this->notificationService->getNotification($id);
        if (!$notification) {
            return response()->json(['message' => 'Notification not found'], 404);
        }
        return response()->json($notification);
    }

    public function store(Request $request): \Illuminate\Http\JsonResponse
    {
        $data = $request->only(['title', 'message', 'user_id']);
        try {
            $notification = $this->notificationService->createNotification($data);
            return response()->json($notification, 201);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['errors' => $e->getMessage()], 400);
        }
    }

    public function update(Request $request, $id): \Illuminate\Http\JsonResponse
    {
        $data = $request->only(['title', 'message', 'is_read', 'user_id']);
        try {
            $notification = $this->notificationService->updateNotification($id, $data);
            if (!$notification) {
                return response()->json(['message' => 'Notification not found'], 404);
            }
            return response()->json($notification);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['errors' => $e->getMessage()], 400);
        }
    }

    public function destroy($id): \Illuminate\Http\JsonResponse
    {
        $deleted = $this->notificationService->deleteNotification($id);
        if (!$deleted) {
            return response()->json(['message' => 'Notification not found'], 404);
        }
        return response()->json(['message' => 'Notification deleted successfully']);
    }
}
