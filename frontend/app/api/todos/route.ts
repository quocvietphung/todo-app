import { NextResponse } from "next/server";

const BACKEND_URL = process.env.BACKEND_URL || "http://localhost:8000/api";

/**
 * API Route: /api/todos
 * ---------------------------------------------------------
 * Acts as a proxy layer between Next.js frontend and the PHP backend.
 * Handles CRUD operations: get, add, update, mark done, and delete.
 */

/**
 * Handles GET requests to fetch all todos.
 *
 * @returns {Promise<NextResponse>} JSON response with todos.
 */
export async function GET(): Promise<NextResponse> {
    try {
        const res = await fetch(`${BACKEND_URL}/todos`, { cache: "no-store" });
        const data = await res.json();
        return NextResponse.json(data, { status: 200 });
    } catch (err) {
        console.error("GET /todos error:", err);
        return NextResponse.json(
            { success: false, error: "Failed to fetch todos" },
            { status: 500 }
        );
    }
}

/**
 * Handles POST requests for CRUD actions (add, done, update, delete).
 *
 * @param {Request} req - The incoming HTTP request.
 * @returns {Promise<NextResponse>} JSON response from backend.
 */
export async function POST(req: Request): Promise<NextResponse> {
    try {
        const body = await req.json();

        // ADD
        if (body.action === "add") {
            const res = await fetch(`${BACKEND_URL}/todos/add`, {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({ title: body.title }),
            });
            const data = await res.json();
            return NextResponse.json(data, { status: 200 });
        }

        // MARK DONE
        if (body.action === "done") {
            const res = await fetch(`${BACKEND_URL}/todos/done`, {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({ id: body.id }),
            });
            const data = await res.json();
            return NextResponse.json(data, { status: 200 });
        }

        // UPDATE TITLE
        if (body.action === "update") {
            const res = await fetch(`${BACKEND_URL}/todos/update`, {
                method: "PUT",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({ id: body.id, title: body.title }),
            });
            const data = await res.json();
            return NextResponse.json(data, { status: 200 });
        }

        // DELETE
        if (body.action === "delete") {
            const res = await fetch(`${BACKEND_URL}/todos/delete`, {
                method: "DELETE",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({ id: body.id }),
            });
            const data = await res.json();
            return NextResponse.json(data, { status: 200 });
        }

        return NextResponse.json(
            { success: false, error: "Invalid action" },
            { status: 400 }
        );
    } catch (err) {
        console.error("POST /todos error:", err);
        return NextResponse.json(
            { success: false, error: "Server error" },
            { status: 500 }
        );
    }
}