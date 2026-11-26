import type { Metadata } from "next";
import "./globals.scss";

export const metadata: Metadata = {
  title: "Site d’art",
  description: "Site d’art en cours de conception",
};

export default function RootLayout({ children }: LayoutProps<"/">) {
  return (
    <html lang="fr">
      <body>{children}</body>
    </html>
  );
}
