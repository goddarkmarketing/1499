import { readFileSync, writeFileSync, mkdirSync } from 'node:fs';
import { dirname, join } from 'node:path';
import { fileURLToPath } from 'node:url';

const root = join(dirname(fileURLToPath(import.meta.url)), '..');
const outRoot = join(root, '_site', 'static-api');

const settings = {
  site_name: 'BoyInsure',
  site_tagline: 'คุ้มครองทุกช่วงชีวิต ด้วยใจ',
  contact_email: 'contact@boyinsure.com',
  phone: '0627878968',
  phone_display: '062-787-8968',
  business_hours: 'จันทร์–ศุกร์ 09:00–18:00 น.',
  address: 'ให้บริการทั่วประเทศ',
  footer_note: 'ศูนย์ไทยประกันชีวิต',
  facebook_url: 'https://www.facebook.com/',
  tiktok_url: 'https://www.tiktok.com/',
  line_url: 'https://line.me/R/ti/p/@boyinsure',
};

const siteRaw = JSON.parse(readFileSync(join(root, 'database', 'cms-site.json'), 'utf8'));
const content = {};
for (const [key, value] of Object.entries(siteRaw)) {
  content[key] = value.body;
}

mkdirSync(join(outRoot, 'site'), { recursive: true });
writeFileSync(
  join(outRoot, 'site', 'public.json'),
  JSON.stringify({ settings, content, source: 'static-demo' }, null, 0)
);

const insRaw = JSON.parse(readFileSync(join(root, 'database', 'cms-insurance.json'), 'utf8'));
const categories = insRaw.categories
  .map((cat) => ({
    id: cat.slug,
    title: cat.title,
    tagline: cat.tagline,
    icon: cat.icon || 'shield',
    plans: (cat.plans || [])
      .filter((plan) => (plan.status ?? 'active') === 'active')
      .map((plan) => ({
        id: plan.slug,
        name: plan.name,
        desc: plan.description,
        image: plan.image,
        featured: Boolean(plan.featured),
        features: plan.features || [],
      })),
  }))
  .filter((cat) => cat.plans.length > 0);

mkdirSync(join(outRoot, 'insurance'), { recursive: true });
writeFileSync(
  join(outRoot, 'insurance', 'categories.json'),
  JSON.stringify({ categories, source: 'static-demo' }, null, 0)
);

console.log('Static demo API written to _site/static-api/');
