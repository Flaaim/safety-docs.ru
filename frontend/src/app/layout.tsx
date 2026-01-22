import type { Metadata } from "next";
import { Noto_Sans } from "next/font/google";
import "./globals.css";



const notoSans = Noto_Sans({
  variable: "--font-noto-sans",
  subsets: ["cyrillic"],
});

export const metadata: Metadata = {
  title: "Блог",
  description: "Мой блог",
};

export default function RootLayout({
  children,
}: Readonly<{
  children: React.ReactNode;
}>) {
  return (
    <html lang="en">
      <body className={`${notoSans.variable}`}>
        {children}
      </body>
    </html>
  );
}
