import { Noto_Sans } from "next/font/google";
import "./globals.css";
import React from "react";


const notoSans = Noto_Sans({
  variable: "--font-noto-sans",
  subsets: ["cyrillic"],
});



export default function RootLayout({
  children,
}: Readonly<{
  children: React.ReactNode;
}>) {
  return (
    <html lang="ru">
      <body className={`${notoSans.variable} antialiased`}>
        {children}
      </body>
    </html>
  );
}
