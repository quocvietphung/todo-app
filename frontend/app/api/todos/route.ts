import { NextResponse } from "next/server";

const BACKEND_URL = process.env.BACKEND_URL || "http://localhost:8000/api";

/**
 * ✅ GET /api/todos
 * Lấy danh sách todos từ backend PHP
 */
export async function GET() {
    try {
        const res = await fetch(`${BACKEND_URL}/todos`, { cache: "no-store" });
        const data = await res.json();
        return NextResponse.json(data, { status: 200 });
    } catch (err) {
        console.error("GET /todos error:", err);
        return NextResponse.json({ success: false, error: "Failed to fetch todos" }, { status: 500 });
    }
}

/**
 * ✅ POST /api/todos
 * Body: { action: "add" | "done", title?: string, id?: number }
 */
export async function POST(req: Request) {
    try {
        const body = await req.json();

        if (body.action === "add") {
            const res = await fetch(`${BACKEND_URL}/todos/add`, {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({ title: body.title }),
            });
            const data = await res.json();
            return NextResponse.json(data, { status: 200 });
        }

        if (body.action === "done") {
            const res = await fetch(`${BACKEND_URL}/todos/done`, {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({ id: body.id }),
            });
            const data = await res.json();
            return NextResponse.json(data, { status: 200 });
        }

        return NextResponse.json({ success: false, error: "Invalid action" }, { status: 400 });
    } catch (err) {
        console.error("POST /todos error:", err);
        return NextResponse.json({ success: false, error: "Server error" }, { status: 500 });
    }
}