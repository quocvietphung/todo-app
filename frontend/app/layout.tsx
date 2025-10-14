import type { Metadata } from "next";
import "./globals.css";

export const metadata: Metadata = {
  title: "Todo App - ARTEMEON Coding Challenge",
  description: "Full-stack Todo App with Next.js frontend and PHP backend",
};

export default function RootLayout({
  children,
}: Readonly<{
  children: React.ReactNode;
}>) {
  return (
    <html lang="en">
      <body className="antialiased">
        {children}
      </body>
    </html>
  );
}
