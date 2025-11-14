import { ArrowRight } from "lucide-react";
import type { Metadata } from "next";
import Image from "next/image";
import Link from "next/link";

export const metadata: Metadata = {
  title: "HTTP Games - Play Games Through API Requests",
  description:
    "An HTTP-based game platform by developers, for developers. Play games like GridTrap by sending API requests - no UI, just pure code.",
  keywords: [
    "HTTP",
    "API",
    "games",
    "developers",
    "REST",
    "programming",
    "cURL",
    "GridTrap",
  ],
  authors: [{ name: "Meldiron", url: "https://github.com/meldiron" }],
  openGraph: {
    title: "HTTP Games - Play Games Through API Requests",
    description:
      "An HTTP-based game platform by developers, for developers. Play games like GridTrap by sending API requests - no UI, just pure code.",
    type: "website",
    url: "https://http-games.almostapps.eu",
    siteName: "HTTP Games",
    locale: "en_US",
    images: [
      {
        url: "/og.png",
        width: 1200,
        height: 630,
        alt: "HTTP Games - Play Games Through API Requests",
        type: "image/png",
      },
    ],
    videos: [],
    audio: [],
    determiner: "auto",
    countryName: "Global",
    alternateLocale: ["en_US"],
    emails: [],
    phoneNumbers: [],
    faxNumbers: [],
  },
  twitter: {
    card: "summary_large_image",
    site: "@http_games",
    creator: "@meldiron",
    title: "HTTP Games - Play Games Through API Requests",
    description:
      "An HTTP-based game platform by developers, for developers. Play games like GridTrap by sending API requests - no UI, just pure code.",
    images: ["/og.png"],
  },
  robots: {
    index: true,
    follow: true,
    googleBot: {
      index: true,
      follow: true,
      "max-video-preview": -1,
      "max-image-preview": "large",
      "max-snippet": -1,
    },
  },
  verification: {
    google: "",
    yandex: "",
    yahoo: "",
    other: {},
  },
  alternates: {
    canonical: "https://http-games.almostapps.eu",
    languages: {
      "en-US": "https://http-games.almostapps.eu",
    },
    media: {},
    types: {
      "application/rss+xml": "https://http-games.almostapps.eu/rss.xml",
    },
  },
  category: "Games",
  classification: "Gaming Platform",
  referrer: "origin-when-cross-origin",
  formatDetection: {
    email: false,
    address: false,
    telephone: false,
  },
  generator: "Next.js",
  applicationName: "HTTP Games",
  appleWebApp: {
    title: "HTTP Games",
    statusBarStyle: "default",
    capable: true,
  },
};

export default function HomePage() {
  return (
    <div className="flex flex-col">
      {/* Hero Section */}
      <section className="relative overflow-hidden bg-linear-to-br from-blue-50 via-white to-purple-50 dark:from-neutral-900 dark:via-neutral-800 dark:to-neutral-900 py-20 sm:py-32 min-h-[calc(100vh-56px)] flex items-center justify-center">
        <div className="absolute inset-0 bg-[url('/grid.svg')] bg-center mask-[linear-gradient(180deg,white,rgba(255,255,255,0))] dark:mask-[linear-gradient(180deg,rgba(255,255,255,0.1),rgba(255,255,255,0))]"></div>
        <div className="relative mx-auto max-w-7xl px-6 lg:px-8">
          <div className="mx-auto max-w-2xl text-center">
            <div className="mb-8 flex justify-center">
              <Image
                src="/logo-on-light.svg"
                alt="HTTP Games Logo"
                width={120}
                height={80}
                className="h-20 block dark:hidden"
              />
              <Image
                src="/logo-on-dark.svg"
                alt="HTTP Games Logo"
                width={120}
                height={80}
                className="h-20 hidden dark:block"
              />
            </div>
            <h1 className="text-5xl font-bold tracking-tight text-neutral-900 dark:text-neutral-100">
              HTTP Games
            </h1>
            <p className="mt-6 text-lg leading-8 text-neutral-600 dark:text-neutral-300">
              An HTTP-based game platform. By developers, for developers. Play
              games by sending API requests - no UI, just pure code.
            </p>
            <div className="mt-10 flex items-center justify-center gap-x-6">
              <Link
                href="/docs"
                className="rounded-lg bg-blue-600 px-4 py-3 text-sm font-semibold text-white shadow-sm hover:bg-blue-500 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600 flex items-center gap-2"
              >
                Get Started
                <ArrowRight className="h-4 w-4" />
              </Link>
              <Link
                href="/docs/grid-trap"
                className="text-sm font-semibold leading-6 text-neutral-900 dark:text-white hover:text-blue-600 dark:hover:text-blue-400 flex items-center gap-2"
              >
                Play GridTrap <span aria-hidden="true">→</span>
              </Link>
            </div>
          </div>
        </div>
      </section>
    </div>
  );
}
